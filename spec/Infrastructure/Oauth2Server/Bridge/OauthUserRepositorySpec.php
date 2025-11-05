<?php

namespace spec\Infrastructure\Oauth2Server\Bridge;

use PhpSpec\ObjectBehavior;
use Trailmind\User\User as TrailmindUser;
use Trailmind\User\UserRepository;
use Infrastructure\Oauth2Server\Bridge\User;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use Trailmind\User\Exception\UserNotFoundException;

class OauthUserRepositorySpec extends ObjectBehavior
{
    function let(
        UserRepository $userRepository
    ) {
        $this->beConstructedWith($userRepository);
    }

    function it_should_be_an_UserRepositoryInterface()
    {
        $this->shouldBeAnInstanceOf(UserRepositoryInterface::class);
    }
    
    function it_should_get_a_user_by_its_credentials(
        UserRepository $userRepository,
        TrailmindUser $futureFarmUser,
        ClientEntityInterface $clientEntity
    ) {
        $futureFarmUser->getId()->willReturn('55');
        $userRepository->findOneByEmail('email')->willReturn($futureFarmUser);
        
        $user = $this->getUserEntityByUserCredentials('email', 'password', 'password', $clientEntity);
        
        $user->shouldBeAnInstanceOf(User::class);
    }
    
    function it_should_not_get_a_user_by_its_credentials_if_email_not_valid(
        UserRepository $userRepository,
        ClientEntityInterface $clientEntity
    ) {
        $userRepository->findOneByEmail('email')->willThrow(UserNotFoundException::class);
        
        $this->getUserEntityByUserCredentials('email', 'password', 'password', $clientEntity)->shouldReturn(null);
    }
}