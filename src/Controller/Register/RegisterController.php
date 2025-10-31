<?php

namespace Controller\Register;

use Dto\Inbound\User\RegisteringUser;
use Dto\Outbound\NoContent;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Trailmind\User\User;
use Trailmind\User\UserRepository;

class RegisterController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository
    ) {}

    #[Route('/v1.0/register', methods: ['POST'], name: 'api_v1.0_register')]
    public function postRegisterAction(
        #[MapRequestPayload(acceptFormat: 'json')] RegisteringUser $registeringUser
    ): NoContent {
        $user = new User(
            $registeringUser->email,
            $registeringUser->name,
            $registeringUser->username,
            ['ROLE_USER']
        );

        $password = $this->passwordHasher->hashPassword($user, $registeringUser->password);
        $user->setPassword($password);

        $this->userRepository->save($user);

        return new NoContent();
    }
}