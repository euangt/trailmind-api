<?php

namespace Infrastructure\Oauth2Server;

use Application\Authentication\AccessToken;
use Application\Dto\Inbound\User\AuthenticatingUser;
use Application\Dto\Inbound\User\ReauthenticatingUser;
use Infrastructure\Oauth2Server\TokenGranter\TokenGranter;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request;
use Trailmind\AuthenticationService\Exception\InvalidAccessTokenException;
use Trailmind\User\Exception\UserNotFoundException;
use Trailmind\User\User;
use Trailmind\User\UserRepository;

class TokenManager
{
    public function __construct(
        private TokenGranter $tokenGranter,
        private ResourceServer $resourceServer,
        private UserRepository $userRepository,
    ) {}

    public function getAccessToken(Request $request, AuthenticatingUser $authenticatingUser): ?AccessToken
    {
        $inputParams = [
            "grant_type" => "password",
            "client_id" => (string) $request->server->get('HTTP_CLIENT_ID'),
            "client_secret" => (string) $request->server->get('HTTP_CLIENT_SECRET'),
            "scope" => "email",
            "username" => $authenticatingUser->email,
            "password" => $authenticatingUser->password,
        ];

        return $this->tokenGranter->grantAccessToken($request, $inputParams);
    }

    public function reauthenticate(Request $request, ReauthenticatingUser $reauthenticatingUser): ?AccessToken
    {
        $inputParams = [
            "grant_type" => "refresh_token",
            "client_id" => (string) $request->server->get('HTTP_CLIENT_ID'),
            "client_secret" => (string) $request->server->get('HTTP_CLIENT_SECRET'),
            "refresh_token" => $reauthenticatingUser->refreshToken,
        ];

        return $this->tokenGranter->refreshAccessToken($request, $inputParams);
    }

    public function findUser(Request $request): ?User
    {
        $authorizationHeader = $request->headers->get('Authorization');

        if ($authorizationHeader === null || trim($authorizationHeader) === '') {
            return null;
        }

        if (! str_starts_with($authorizationHeader, 'Bearer ')) {
            throw new InvalidAccessTokenException();
        }

        try {
            $validatedRequest = $this->resourceServer->validateAuthenticatedRequest($this->convertRequest($request));
            $accessTokenId = $validatedRequest->getAttribute('oauth_access_token_id');

            if (! is_string($accessTokenId) || $accessTokenId === '') {
                throw new InvalidAccessTokenException();
            }

            return $this->userRepository->findOneByAccessToken($accessTokenId);
        } catch (OAuthServerException | UserNotFoundException $exception) {
            throw new InvalidAccessTokenException();
        }
    }

    private function convertRequest(Request $request): ServerRequestInterface
    {
        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);

        return $psrHttpFactory->createRequest($request);
    }
}
