#!/bin/sh
# =============================================================================
# Trailmind API — ECS container entrypoint
#
# All runtime secrets are supplied by the ECS task definition via AWS Secrets
# Manager references.  This script does NOT generate or default any secret
# value; if a required variable is absent the container exits immediately
# rather than starting in an incomplete or insecure state.
#
# Required environment variables (set via ECS task definition secrets):
#
#   APP_SECRET               Symfony application secret
#   DATABASE_URL             PostgreSQL connection string
#   OAUTH2_PRIVATE_KEY_PEM   RSA private key — PEM file contents
#   OAUTH2_PUBLIC_KEY_PEM    RSA public key  — PEM file contents
#   OAUTH2_ENCRYPTION_KEY    OAuth2 token encryption key
#   DEFAULT_URI              Public base URL  (e.g. https://api.example.com)
#
# Optional:
#   OAUTH2_PASSPHRASE        Private key passphrase (only if key is encrypted)
#
# Key material handling:
#   OAUTH2_PRIVATE_KEY_PEM and OAUTH2_PUBLIC_KEY_PEM are written to a private
#   subdirectory of /run/secrets/ (tmpfs on ECS Fargate) with restrictive
#   permissions.  The _PEM environment variables are then unset so they do not
#   appear in the environment of the child Apache process.  The Symfony config
#   reads OAUTH2_PRIVATE_KEY / OAUTH2_PUBLIC_KEY as file paths, which this
#   script sets to the written locations.
# =============================================================================
set -eu

# ---------------------------------------------------------------------------
# Helpers
# ---------------------------------------------------------------------------

err() {
    printf 'entrypoint: error: %s\n' "$1" >&2
    exit 1
}

require() {
    eval "_val=\${$1:-}"
    [ -n "$_val" ] || err "required variable '$1' is not set — set it via the ECS task definition secrets"
}

# ---------------------------------------------------------------------------
# Validate all required secrets before touching the filesystem
# ---------------------------------------------------------------------------

require APP_SECRET
require DATABASE_URL
require OAUTH2_PRIVATE_KEY_PEM
require OAUTH2_PUBLIC_KEY_PEM
require OAUTH2_ENCRYPTION_KEY
require DEFAULT_URI

# ---------------------------------------------------------------------------
# Materialise key files on tmpfs
#
# ECS Fargate mounts /run as an in-memory tmpfs.  We write key content there
# so it is never stored on any persistent layer.
# ---------------------------------------------------------------------------

SECRETS_DIR=/run/secrets/trailmind

mkdir -p "$SECRETS_DIR"
chmod 700 "$SECRETS_DIR"

# Write private key — owner-read/write only (mode 0600).
# A subshell with a restrictive umask ensures the file is created with the
# correct permissions atomically; printf avoids exposing content in the
# process argument list.
(umask 177 && printf '%s' "$OAUTH2_PRIVATE_KEY_PEM" > "$SECRETS_DIR/signing.pem")
chown www-data:www-data "$SECRETS_DIR/signing.pem"

# Write public key — owner-read/write only (mode 0600).
(umask 177 && printf '%s' "$OAUTH2_PUBLIC_KEY_PEM" > "$SECRETS_DIR/verification.pem")
chown www-data:www-data "$SECRETS_DIR/verification.pem"

# ---------------------------------------------------------------------------
# Export file-path variables consumed by the Symfony configuration and unset
# the PEM content variables so they are absent from the child process
# environment (including /proc/<pid>/environ after exec).
# ---------------------------------------------------------------------------

export OAUTH2_PRIVATE_KEY="$SECRETS_DIR/signing.pem"
export OAUTH2_PUBLIC_KEY="$SECRETS_DIR/verification.pem"

unset OAUTH2_PRIVATE_KEY_PEM
unset OAUTH2_PUBLIC_KEY_PEM

# ---------------------------------------------------------------------------
# Hand off to the command passed to this entrypoint (default: apache2-foreground)
# ---------------------------------------------------------------------------

exec "$@"
