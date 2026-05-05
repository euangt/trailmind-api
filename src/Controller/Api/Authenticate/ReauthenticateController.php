<?php

namespace Controller\Api\Authenticate;

use Application\Dto\Inbound\User\ReauthenticatingUser;
use Dto\Outbound\Authentication\AccessTokenBuilder;
use Dto\Outbound\Success;
use Infrastructure\Oauth2Server\TokenManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Trailmind\AuthenticationService\Exception\TokenRequestException;
use Trailmind\AuthenticationService\Exception\UnableToCreateAccessTokenException;

class ReauthenticateController
{
    public function __construct(
        private TokenManager $tokenManager,
        private AccessTokenBuilder $accessTokenBuilder,
    ) {}

    #[Route('/v1.0/reauthenticate', methods: ['POST'], name: 'api_v1.0_reauthenticate')]
    public function postReauthenticateAction(
        #[MapRequestPayload(acceptFormat: 'json')]
        ReauthenticatingUser $reauthenticatingUser,
        Request $request,
    ): Success|JsonResponse {
        try {
            $accessToken = $this->tokenManager->reauthenticate($request, $reauthenticatingUser);
        } catch (TokenRequestException $tre) {
            return new JsonResponse($tre->getPayload(), $tre->getStatusCode());
        } catch (UnableToCreateAccessTokenException $utcate) {
            return new JsonResponse([
                'error' => 'server_error',
                'error_description' => 'Unable to refresh access token',
            ], 500);
        }

        return $this->accessTokenBuilder
            ->setContext('v1.0_reauthenticate')
            ->build($accessToken);
    }
}
