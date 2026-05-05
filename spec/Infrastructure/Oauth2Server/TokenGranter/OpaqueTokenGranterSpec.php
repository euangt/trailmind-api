<?php

namespace spec\Infrastructure\Oauth2Server\TokenGranter;

use Infrastructure\Oauth2Server\TokenGranter\TokenGranter;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Request;
use Trailmind\AuthenticationService\Exception\InvalidCredentialsException;
use Trailmind\AuthenticationService\Exception\InvalidRefreshTokenException;
use Trailmind\AuthenticationService\Exception\TokenRequestException;

class OpaqueTokenGranterSpec extends ObjectBehavior
{
    function let(
        AuthorizationServer $authorizationServer
    ) {
        $this->beConstructedWith($authorizationServer);
    }

    function it_should_be_an_instance_of_TokenGranter()
    {
        $this->shouldImplement(TokenGranter::class);
    }

    function it_should_throw_invalid_credentials_exception_for_an_invalid_password_grant(
    ) {
        $this->beConstructedWith(
            new ThrowingAuthorizationServer(OAuthServerException::invalidCredentials())
        );

        $request = Request::create('/v1.0/authenticate', 'POST');

        $this->shouldThrow(InvalidCredentialsException::class)
            ->duringGrantAccessToken($request, [
                'grant_type' => 'password',
                'client_id' => 'client-id',
                'client_secret' => 'client-secret',
                'username' => 'user@example.com',
                'password' => 'wrong-password',
            ]);
    }

    function it_should_throw_invalid_refresh_token_exception_for_an_invalid_refresh_grant(
    ) {
        $this->beConstructedWith(
            new ThrowingAuthorizationServer(OAuthServerException::invalidRefreshToken())
        );

        $request = Request::create('/v1.0/reauthenticate', 'POST');

        $this->shouldThrow(InvalidRefreshTokenException::class)
            ->duringRefreshAccessToken($request, [
                'grant_type' => 'refresh_token',
                'client_id' => 'client-id',
                'client_secret' => 'client-secret',
                'refresh_token' => 'invalid-refresh-token',
            ]);
    }

    function it_should_preserve_oauth_status_and_payload_for_other_oauth_errors()
    {
        $this->beConstructedWith(
            new ThrowingAuthorizationServer(OAuthServerException::invalidRequest('client_id'))
        );

        $request = Request::create('/v1.0/authenticate', 'POST');

        try {
            $this->grantAccessToken($request, [
                'grant_type' => 'password',
                'username' => 'user@example.com',
                'password' => 'password',
            ]);
        } catch (TokenRequestException $exception) {
            if ($exception->getStatusCode() !== 400) {
                throw new \RuntimeException('Expected status code 400.');
            }

            if ($exception->getPayload() !== OAuthServerException::invalidRequest('client_id')->getPayload()) {
                throw new \RuntimeException('Expected the OAuth payload to be preserved.');
            }

            return;
        }

        throw new \RuntimeException('Expected a token request exception to be thrown.');
    }
}

final class ThrowingAuthorizationServer extends AuthorizationServer
{
    public function __construct(
        private OAuthServerException $exception,
    ) {}

    public function respondToAccessTokenRequest(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        throw $this->exception;
    }
}
