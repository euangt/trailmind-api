<?php

namespace Infrastructure\Oauth2Server\TokenGranter;

use Dto\Internal\Authentication\AccessToken;
use Symfony\Component\HttpFoundation\Request;

interface TokenGranter
{
    public function grantAccessToken(Request $request, array $inputParams): ?AccessToken;

    public function refreshAccessToken(Request $request, array $inputParams): ?AccessToken;
}