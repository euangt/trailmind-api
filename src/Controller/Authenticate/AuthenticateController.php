<?php

namespace Controller\Authenticate;

use Application\ValueResolver\CustomisableValueResolver;
use Dto\Inbound\User\AuthenticatingUser;
use Dto\Outbound\Success;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Trailmind\AuthenticationService\PasswordVerifier;
use Trailmind\User\User;

class AuthenticateController
{
    public function __construct(
        private PasswordVerifier $passwordVerifier,
    ) {}

    #[Route('/v1.0/authenticate', methods: ['POST'], name: 'api_v1.0_authenticate')]
    public function postAuthenticateAction(
        #[MapRequestPayload(acceptFormat: 'json')] AuthenticatingUser $authenticatingUser,
        #[CustomisableValueResolver('entity', false, ['class' => User::class, 'mapping' => ['email' => 'email']])] $user,
    ): Success {
        if (!$this->passwordVerifier->verifyPassword($user, $authenticatingUser->password)) {
            throw new UnauthorizedHttpException('Invalid credentials');
        }

        return new Success();
    }
}