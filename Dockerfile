# =============================================================================
# Stage 1 — vendor
#
# Installs only production Composer dependencies on a minimal PHP CLI image.
# Build tools (Composer, unzip) never reach the runtime stage.
# =============================================================================
FROM php:8.4-cli AS vendor

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        curl \
        libpq-dev \
        unzip \
    && docker-php-ext-install pdo_pgsql pgsql \
    && curl -fsSL https://getcomposer.org/installer \
       | php -- --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY composer.json composer.lock symfony.lock ./

# --classmap-authoritative: builds a complete static class map so no filesystem
# lookups are needed at runtime and the autoloader surface is minimised.
RUN composer install \
    --prefer-dist \
    --no-dev \
    --classmap-authoritative \
    --no-interaction \
    --no-progress \
    --no-scripts

# =============================================================================
# Stage 2 — runtime
#
# Lean Apache + PHP 8.4 image.  Contains no build tools, no dev dependencies,
# and no baked-in secrets.  All sensitive values are injected at runtime by the
# ECS task definition via AWS Secrets Manager references.
# =============================================================================
FROM php:8.4-apache

ARG DEBIAN_FRONTEND=noninteractive

# Runtime OS packages — only what the PHP extensions require at runtime.
# curl is also included for the HEALTHCHECK probe.
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        curl \
        libicu-dev \
        libpq-dev \
        libzip-dev \
    && docker-php-ext-install \
        intl \
        opcache \
        pdo_pgsql \
        pgsql \
        zip \
    && rm -rf /var/lib/apt/lists/*

# PHP production configuration
RUN { \
        echo 'expose_php = Off'; \
        echo 'display_errors = Off'; \
        echo 'log_errors = On'; \
        echo 'error_log = /proc/self/fd/2'; \
        echo 'opcache.enable = 1'; \
        echo 'opcache.validate_timestamps = 0'; \
        echo 'opcache.revalidate_freq = 0'; \
        echo 'opcache.max_accelerated_files = 20000'; \
        echo 'opcache.memory_consumption = 256'; \
        echo 'opcache.interned_strings_buffer = 16'; \
        echo 'upload_max_filesize = 8M'; \
        echo 'post_max_size = 8M'; \
    } > /usr/local/etc/php/conf.d/trailmind.ini

# Apache: enable required modules, point document root at Symfony's public/,
# and harden the server disclosure headers.
RUN a2enmod rewrite headers \
    && sed -ri -e 's!/var/www/html!/var/www/html/public!g' \
        /etc/apache2/sites-available/000-default.conf \
        /etc/apache2/apache2.conf \
        /etc/apache2/conf-available/*.conf \
    && printf '\nServerTokens Prod\nServerSignature Off\nTraceEnable Off\n' \
        >> /etc/apache2/conf-enabled/security.conf \
    && printf '<IfModule mod_headers.c>\n\
    Header always set X-Content-Type-Options "nosniff"\n\
    Header always set X-Frame-Options "DENY"\n\
    Header always set Referrer-Policy "strict-origin-when-cross-origin"\n\
    Header always unset X-Powered-By\n\
</IfModule>\n' \
        >> /etc/apache2/conf-enabled/security.conf

WORKDIR /var/www/html

# Hard-code production environment defaults so the app cannot accidentally
# boot in dev mode if the ECS task definition omits these variables.
# Real env vars from the task definition always take precedence.
ENV APP_ENV=prod \
    APP_DEBUG=0

# Bring in the production vendor directory from the build stage.
COPY --from=vendor /app/vendor ./vendor

# Copy only the files the application needs at runtime.
# Tests, fixtures, specs, and local dev tooling are deliberately excluded.
COPY .env              ./
COPY composer.json     ./
COPY composer.lock     ./
COPY importmap.php     ./
COPY symfony.lock      ./
COPY assets            ./assets
COPY bin/console       ./bin/console
COPY config            ./config
COPY public            ./public
COPY src               ./src
COPY templates         ./templates
COPY translations      ./translations

COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod 500 /usr/local/bin/entrypoint

# Build-time asset compilation
#
# Symfony's DI container must boot to run `tailwind:build` and
# `asset-map:compile`, and booting requires valid OAuth key file paths.
#
# We generate a throwaway RSA pair using PHP's built-in OpenSSL extension
# (no openssl CLI tool required), compile the assets, then immediately delete
# the key files — all within this single RUN instruction.  Because creation and
# deletion happen in the same layer, the key material is never committed to any
# image layer and cannot be recovered from the image.
#
# The placeholder values below are NOT production secrets; they exist only to
# allow the container to boot for the asset build step.
RUN php -r '
        $key = openssl_pkey_new([
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ]);
        openssl_pkey_export($key, $priv);
        $pub = openssl_pkey_get_details($key)["key"];
        file_put_contents("/tmp/.btk-priv.pem", $priv);
        file_put_contents("/tmp/.btk-pub.pem",  $pub);
    ' \
    && APP_ENV=prod \
       APP_DEBUG=0 \
       APP_SECRET=build-time-placeholder \
       DEFAULT_URI=https://placeholder.invalid \
       DATABASE_URL="postgresql://x:x@localhost/x?serverVersion=16" \
       OAUTH2_PRIVATE_KEY=/tmp/.btk-priv.pem \
       OAUTH2_PUBLIC_KEY=/tmp/.btk-pub.pem \
       OAUTH2_PASSPHRASE= \
       OAUTH2_ENCRYPTION_KEY=build-time-placeholder \
       php bin/console tailwind:build --minify \
    && APP_ENV=prod \
       APP_DEBUG=0 \
       APP_SECRET=build-time-placeholder \
       DEFAULT_URI=https://placeholder.invalid \
       DATABASE_URL="postgresql://x:x@localhost/x?serverVersion=16" \
       OAUTH2_PRIVATE_KEY=/tmp/.btk-priv.pem \
       OAUTH2_PUBLIC_KEY=/tmp/.btk-pub.pem \
       OAUTH2_PASSPHRASE= \
       OAUTH2_ENCRYPTION_KEY=build-time-placeholder \
       php bin/console asset-map:compile \
    && rm -f /tmp/.btk-priv.pem /tmp/.btk-pub.pem \
    && mkdir -p var/cache var/log \
    && chown -R www-data:www-data var public

# Verify Apache is responding.  ECS target-group health checks at the ALB
# layer are the primary liveness signal, but this container-level check gives
# early failure detection before the ALB probe fires.
HEALTHCHECK --interval=30s --timeout=5s --start-period=60s --retries=3 \
    CMD curl -sf --max-time 4 http://localhost/ -o /dev/null || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint"]
CMD ["apache2-foreground"]
