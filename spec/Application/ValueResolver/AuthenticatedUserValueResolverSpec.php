<?php

namespace spec\Application\ValueResolver;

use Application\ValueResolver\AuthenticatedUserValueResolver;
use Application\ValueResolver\CoreValueResolver;
use Application\ValueResolver\CustomisableValueResolver;
use Infrastructure\Oauth2Server\TokenManager;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Trailmind\AuthenticationService\Exception\InvalidAccessTokenException;
use Trailmind\User\User;

class AuthenticatedUserValueResolverSpec extends ObjectBehavior
{
    function it_should_be_a_ValueResolver()
    {
        assert ($this->getWrappedObject() instanceof ValueResolverInterface);
    }

    function it_should_be_a_CoreValueResolver()
    {
        assert ($this->getWrappedObject() instanceof CoreValueResolver);
    }

    function let(
        ArgumentMetadata $argument,
        TokenManager $tokenManager,
        CustomisableValueResolver $customisableValueResolver
    ) {
        $this->beConstructedWith($tokenManager);

        $argument->getAttributes()->willReturn([$customisableValueResolver]);
        $customisableValueResolver->getOptions()->willReturn([]);
    }

    function it_should_resolve_with_an_authenticated_user(
        ArgumentMetadata $argument,
        Request $request,
        TokenManager $tokenManager,
        User $user
    ) {
        $tokenManager->findUser($request)->shouldBeCalled()->willReturn($user);

        $this->resolve($request, $argument)->shouldReturn([$user]);
    }

    function it_should_resolve_with_a_nullable_authenticated_user(
        ArgumentMetadata $argument,
        Request $request,
        TokenManager $tokenManager,
        CustomisableValueResolver $customisableValueResolver
    ) {
        $customisableValueResolver->getOptions()->willReturn(['nullable' => true]);
        $tokenManager->findUser($request)->shouldBeCalled()->willReturn(null);

        $this->resolve($request, $argument)->shouldReturn([]);
    }

    function it_should_throw_if_authentication_token_is_missing_for_non_nullable_user(
        ArgumentMetadata $argument,
        Request $request,
        TokenManager $tokenManager
    ) {
        $tokenManager->findUser($request)->shouldBeCalled()->willReturn(null);

        $this->shouldThrow(UnauthorizedHttpException::class)->duringResolve($request, $argument);
    }

    function it_should_throw_if_authentication_token_is_invalid(
        ArgumentMetadata $argument,
        Request $request,
        TokenManager $tokenManager
    ) {
        $tokenManager->findUser($request)->shouldBeCalled()->willThrow(InvalidAccessTokenException::class);

        $this->shouldThrow(UnauthorizedHttpException::class)->duringResolve($request, $argument);
    }
}
