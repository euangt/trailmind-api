<?php

namespace Infrastructure\Security\Authenticator;

use Infrastructure\Oauth2Server\TokenManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Trailmind\AuthenticationService\Exception\InvalidAccessTokenException;

class BearerTokenAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    public function __construct(
        private readonly TokenManager $tokenManager,
    ) {}

    public function supports(Request $request): ?bool
    {
        $authorizationHeader = $request->headers->get('Authorization');

        return $authorizationHeader !== null && trim($authorizationHeader) !== '';
    }

    public function authenticate(Request $request): Passport
    {
        try {
            $user = $this->tokenManager->findUser($request);
        } catch (InvalidAccessTokenException $exception) {
            throw new CustomUserMessageAuthenticationException('Invalid authentication token');
        }

        if ($user === null) {
            throw new CustomUserMessageAuthenticationException('Authentication token required');
        }

        $request->attributes->set('_current_user_resolved', true);
        $request->attributes->set('_current_user', $user);

        return new SelfValidatingPassport(
            new UserBadge($user->getEmail(), static fn () => $user),
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'error' => 'invalid_token',
            'error_description' => $exception->getMessageKey(),
        ], Response::HTTP_UNAUTHORIZED, [
            'WWW-Authenticate' => 'Bearer',
        ]);
    }

    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        return new JsonResponse([
            'error' => 'authentication_required',
            'error_description' => 'Authentication token required',
        ], Response::HTTP_UNAUTHORIZED, [
            'WWW-Authenticate' => 'Bearer',
        ]);
    }
}