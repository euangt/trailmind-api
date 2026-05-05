<?php

namespace Infrastructure\Oauth2Server\TokenGranter;

use Application\Authentication\AccessToken;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response as Psr7Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request;
use Trailmind\AuthenticationService\Exception\InvalidCredentialsException;
use Trailmind\AuthenticationService\Exception\InvalidRefreshTokenException;
use Trailmind\AuthenticationService\Exception\TokenRequestException;
use Trailmind\AuthenticationService\Exception\UnableToCreateAccessTokenException;

class OpaqueTokenGranter implements TokenGranter
{
    public function __construct(
        private AuthorizationServer $authorizationServer,
    ) {}

    /**
     * @param array<string, string> $inputParams
     */
    public function grantAccessToken(Request $request, array $inputParams): AccessToken
    {
        return $this->handleTokenRequest(
            $request,
            $inputParams,
            static fn (OAuthServerException $exception): UnableToCreateAccessTokenException =>
                $exception->getErrorType() === 'invalid_grant'
                    ? new InvalidCredentialsException($exception->getPayload())
                    : TokenRequestException::fromOAuthException($exception)
        );
    }

    /**
     * @param array<string, string> $inputParams
     */
    public function refreshAccessToken(Request $request, array $inputParams): AccessToken
    {
        return $this->handleTokenRequest(
            $request,
            $inputParams,
            static fn (OAuthServerException $exception): UnableToCreateAccessTokenException =>
                $exception->getErrorType() === 'invalid_grant'
                    ? new InvalidRefreshTokenException($exception->getPayload())
                    : TokenRequestException::fromOAuthException($exception)
        );
    }

    /**
     * @param array<string, string> $inputParams
     */
    private function handleTokenRequest(
        Request $request,
        array $inputParams,
        callable $exceptionMapper
    ): AccessToken {
        $psrRequest = $this->convertRequest($request, $inputParams);

        return $this->withErrorHandling(function () use ($psrRequest) {
            $response = $this->authorizationServer->respondToAccessTokenRequest($psrRequest, new Psr7Response());

            return $this->extractTokens($response);
        }, $exceptionMapper);
    }

    /**
     * @param array<string, string> $inputParams
     */
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

    private function withErrorHandling(callable $callback, callable $exceptionMapper): AccessToken
    {
        try {
            return $callback();
        } catch (OAuthServerException $e) {
            throw $exceptionMapper($e);
        }
    }
}
