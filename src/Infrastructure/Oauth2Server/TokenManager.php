<?php

namespace Infrastructure\Oauth2Server;

use Application\Authentication\AccessToken;
use Application\Dto\Inbound\User\AuthenticatingUser;
use Application\Dto\Inbound\User\ReauthenticatingUser;
use Infrastructure\Oauth2Server\TokenGranter\TokenGranter;
use Symfony\Component\HttpFoundation\Request;

class TokenManager
{
    public function __construct(
        private TokenGranter $tokenGranter,
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
}
