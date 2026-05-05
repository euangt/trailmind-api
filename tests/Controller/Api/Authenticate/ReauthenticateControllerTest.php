<?php

namespace App\Tests\Controller\Api\Authenticate;

use Application\Authentication\AccessToken;
use Infrastructure\Oauth2Server\TokenManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Trailmind\AuthenticationService\Exception\InvalidRefreshTokenException;

final class ReauthenticateControllerTest extends WebTestCase
{
    public function testReauthenticateReturnsAccessTokenPayload(): void
    {
        $client = static::createClient();
        $tokenManager = $this->createMock(TokenManager::class);

        $tokenManager->method('reauthenticate')->willReturn(
            new AccessToken('new-access-token', 'new-refresh-token', 86400.0)
        );

        static::getContainer()->set(TokenManager::class, $tokenManager);

        $client->request(
            'POST',
            '/v1.0/reauthenticate',
            server: [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_CLIENT_ID' => 'client-id',
                'HTTP_CLIENT_SECRET' => 'client-secret',
            ],
            content: json_encode([
                'refresh_token' => 'refresh-token',
            ], JSON_THROW_ON_ERROR),
        );

        self::assertResponseIsSuccessful();
        self::assertSame([
            'access_token' => 'new-access-token',
            'refresh_token' => 'new-refresh-token',
            'expires_in' => 86400,
        ], json_decode((string) $client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR));
    }

    public function testReauthenticateReturnsInvalidRefreshTokenPayload(): void
    {
        $client = static::createClient();
        $tokenManager = $this->createMock(TokenManager::class);

        $tokenManager->method('reauthenticate')->willThrowException(new InvalidRefreshTokenException());

        static::getContainer()->set(TokenManager::class, $tokenManager);

        $client->request(
            'POST',
            '/v1.0/reauthenticate',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'refresh_token' => 'invalid-refresh-token',
            ], JSON_THROW_ON_ERROR),
        );

        self::assertResponseStatusCodeSame(401);
        self::assertSame([
            'error' => 'invalid_grant',
            'error_description' => 'The refresh token is invalid.',
        ], json_decode((string) $client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR));
    }
}
