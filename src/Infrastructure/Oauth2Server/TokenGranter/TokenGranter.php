<?php

namespace Infrastructure\Oauth2Server\TokenGranter;

use Application\Authentication\AccessToken;
use Symfony\Component\HttpFoundation\Request;

interface TokenGranter
{
    /**
     * @param array<string, string> $inputParams
     */
    public function grantAccessToken(Request $request, array $inputParams): ?AccessToken;

    /**
     * @param array<string, string> $inputParams
     */
    public function refreshAccessToken(Request $request, array $inputParams): ?AccessToken;
}