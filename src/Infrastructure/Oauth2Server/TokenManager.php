<?php

namespace Infrastructure\Oauth2Server;

use Dto\Internal\Authentication\AccessToken;
use Infrastructure\Oauth2Server\TokenGranter\TokenGranter;
use Symfony\Component\HttpFoundation\Request;
use Trailmind\User\User;

class TokenManager
{
    public function __construct(
        private TokenGranter $tokenGranter,
    ) {}

    /**
     * @return Token|null
     */
    public function getAccessToken(Request $request, User $user): ?AccessToken
    {
        $inputParams = [
            "grant_type" => "password",
            "client_id" => $request->server->get('HTTP_CLIENT_ID'),
            "client_secret" => $request->server->get('HTTP_CLIENT_SECRET'),
            "scope" => "*",
            "username" => $user->getEmail(),
            "password" => "authenticated",  // Any non-empty string as password has already been verified
        ];

        return $this->tokenGranter->grantAccessToken($request, $inputParams);
    }
}
