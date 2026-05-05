<?php

namespace spec\Infrastructure\Security;

use Infrastructure\Oauth2Server\TokenManager;
use Infrastructure\Security\CurrentUserProvider;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Trailmind\AuthenticationService\Exception\InvalidAccessTokenException;
use Trailmind\User\User;

class CurrentUserProviderSpec extends ObjectBehavior
{
    function let(
        RequestStack $requestStack,
        TokenManager $tokenManager
    ) {
        $this->beConstructedWith($requestStack, $tokenManager);
    }

    function it_should_return_null_when_there_is_no_current_request(
        RequestStack $requestStack
    ) {
        $requestStack->getCurrentRequest()->willReturn(null);

        $this->findUser()->shouldReturn(null);
    }

    function it_should_return_the_current_user_from_the_token_manager(
        RequestStack $requestStack,
        TokenManager $tokenManager,
        User $user
    ) {
        $request = Request::create('/v1.0/trails', 'GET');

        $requestStack->getCurrentRequest()->willReturn($request);
        $tokenManager->findUser($request)->shouldBeCalled()->willReturn($user);

        $this->findUser()->shouldReturn($user);
    }

    function it_should_cache_the_resolved_user_for_the_current_request(
        RequestStack $requestStack,
        TokenManager $tokenManager,
        User $user
    ) {
        $request = Request::create('/v1.0/trails', 'GET');

        $requestStack->getCurrentRequest()->willReturn($request);
        $tokenManager->findUser($request)->shouldBeCalled()->willReturn($user);

        $this->findUser()->shouldReturn($user);
        $this->findUser()->shouldReturn($user);
    }

    function it_should_allow_invalid_access_token_exceptions_to_bubble(
        RequestStack $requestStack,
        TokenManager $tokenManager
    ) {
        $request = Request::create('/v1.0/trails', 'GET');

        $requestStack->getCurrentRequest()->willReturn($request);
        $tokenManager->findUser($request)->shouldBeCalled()->willThrow(InvalidAccessTokenException::class);

        $this->shouldThrow(InvalidAccessTokenException::class)->duringFindUser();
    }
}
