<?php

namespace Infrastructure\Oauth2Server\TokenGranter;

use Dto\Internal\Authentication\AccessToken;
use Symfony\Component\HttpFoundation\Request;

interface TokenGranter
{
    /**
     * @param Request $request
     * @param array $inputParams
     * 
     * @return AccessToken
     */
    public function grantAccessToken(Request $request, array $inputParams): ?AccessToken;

    /**
     * @param Request $request
     * @param array $inputParams
     * 
     * @return AccessToken
     */
    public function refreshAccessToken(Request $request, array $inputParams): ?AccessToken;
}