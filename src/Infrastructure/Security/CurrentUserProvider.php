<?php

namespace Infrastructure\Security;

use Infrastructure\Oauth2Server\TokenManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Trailmind\AuthenticationService\Exception\InvalidAccessTokenException;
use Trailmind\User\User;

class CurrentUserProvider
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly TokenManager $tokenManager,
    ) {}

    public function findUser(): ?User
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null) {
            return null;
        }

        if ($request->attributes->has('_current_user_resolved')) {
            $user = $request->attributes->get('_current_user');

            return $user instanceof User ? $user : null;
        }

        $user = $this->tokenManager->findUser($request);

        $request->attributes->set('_current_user_resolved', true);

        if ($user !== null) {
            $request->attributes->set('_current_user', $user);
        }

        return $user;
    }
}
