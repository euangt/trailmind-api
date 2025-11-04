<?php

namespace Controller\Authenticate;

use Application\ValueResolver\CustomisableValueResolver;
use Dto\Inbound\User\AuthenticatingUser;
use Dto\Outbound\Success;
use Infrastructure\Oauth2Server\TokenManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Trailmind\AuthenticationService\Exception\UnableToCreateAccessTokenException;
use Trailmind\AuthenticationService\PasswordVerifier;
use Trailmind\User\User;

class AuthenticateController
{
    public function __construct(
        private PasswordVerifier $passwordVerifier,
        private TokenManager $tokenManager,
    ) {}

    #[Route('/v1.0/authenticate', methods: ['POST'], name: 'api_v1.0_authenticate')]
    public function postAuthenticateAction(
        #[MapRequestPayload(acceptFormat: 'json')] AuthenticatingUser $authenticatingUser,
        #[CustomisableValueResolver('entity', false, ['class' => User::class, 'mapping' => ['email' => 'email']])] $user,
        Request $request,
    ): Success {
        if (!$this->passwordVerifier->verifyPassword($user, $authenticatingUser->password)) {
            throw new UnauthorizedHttpException('Invalid credentials');
        }

        try {
            $accessToken = $this->tokenManager->getAccessToken($request, $user);
        } catch (UnableToCreateAccessTokenException $utcate) {
            throw new BadRequestHttpException('Unable to create access token');
        }

        return new Success();
    }
}