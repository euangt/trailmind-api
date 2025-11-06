<?php

namespace Infrastructure\Oauth2Server\TokenGranter;

use Dto\Internal\Authentication\AccessToken;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AbstractGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response as Psr7Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request;
use Trailmind\AuthenticationService\Exception\UnableToCreateAccessTokenException;

class OpaqueTokenGranter implements TokenGranter
{
    private const ACCESS_TOKEN_TTL = 'P1D';

    private const REFRESH_TOKEN_TTL = 'P1M';

    public function __construct(
        private AuthorizationServer $authorizationServer,
        private PasswordGrant $passwordGrant,
        private RefreshTokenGrant $refreshTokenGrant,
    ) {}

    public function grantAccessToken(Request $request, array $inputParams): AccessToken
    {
        return $this->handleGrant($request, $inputParams, $this->passwordGrant, self::ACCESS_TOKEN_TTL, self::REFRESH_TOKEN_TTL);
    }

    public function refreshAccessToken(Request $request, array $inputParams): AccessToken
    {
        return $this->handleGrant($request, $inputParams, $this->refreshTokenGrant, self::ACCESS_TOKEN_TTL, self::REFRESH_TOKEN_TTL);
    }

    private function handleGrant(
        Request $request,
        array $inputParams,
        AbstractGrant $grant,
        string $accessTokenTTL,
        string $refreshTokenTTL
    ): AccessToken {
        $psrRequest = $this->convertRequest($request, $inputParams);

        return $this->withErrorHandling(function () use ($grant, $accessTokenTTL, $refreshTokenTTL, $psrRequest) {
            $grant->setRefreshTokenTTL(new \DateInterval($refreshTokenTTL));
            $this->authorizationServer->enableGrantType($grant, new \DateInterval($accessTokenTTL));

            $response = $this->authorizationServer->respondToAccessTokenRequest($psrRequest, new Psr7Response());
            return $this->extractTokens($response);
        });
    }

    private function convertRequest(Request $request, array $inputParams): ServerRequestInterface
    {
        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $psrRequest = $psrHttpFactory->createRequest($request);
        return $psrRequest->withParsedBody($inputParams);
    }

    private function extractTokens(ResponseInterface $response): AccessToken
    {
        $content = json_decode((string) $response->getBody(), false, 512, JSON_THROW_ON_ERROR);
        return new AccessToken(
            $content->access_token ?? null,
            $content->refresh_token ?? null,
            $content->expires_in ?? null
        );
    }

    private function withErrorHandling(callable $callback): AccessToken
    {
        try {
            return $callback();
        } catch (OAuthServerException $e) {
            throw new UnableToCreateAccessTokenException();
        }
    }
}