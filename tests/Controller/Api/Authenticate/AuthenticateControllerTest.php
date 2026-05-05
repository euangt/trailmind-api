<?php

namespace App\Tests\Controller\Api\Authenticate;

use Application\Authentication\AccessToken;
use Infrastructure\Oauth2Server\TokenManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Trailmind\AuthenticationService\Exception\TokenRequestException;

final class AuthenticateControllerTest extends WebTestCase
{
    public function testAuthenticateReturnsAccessTokenPayload(): void
    {
        $client = static::createClient();
        $tokenManager = $this->createMock(TokenManager::class);

        $tokenManager->method('getAccessToken')->willReturn(
            new AccessToken('access-token', 'refresh-token', 86400.0)
        );

        static::getContainer()->set(TokenManager::class, $tokenManager);

        $client->request(
            'POST',
            '/v1.0/authenticate',
            server: [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_CLIENT_ID' => 'client-id',
                'HTTP_CLIENT_SECRET' => 'client-secret',
            ],
            content: json_encode([
                'email' => 'user@example.com',
                'password' => 'password',
            ], JSON_THROW_ON_ERROR),
        );

        self::assertResponseIsSuccessful();
        self::assertSame([
            'access_token' => 'access-token',
            'refresh_token' => 'refresh-token',
            'expires_in' => 86400,
        ], json_decode((string) $client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR));
    }

    public function testAuthenticateReturnsOauthErrorPayloadForClientFailures(): void
    {
        $client = static::createClient();
        $tokenManager = $this->createMock(TokenManager::class);

        $tokenManager->method('getAccessToken')->willThrowException(
            new TokenRequestException([
                'error' => 'invalid_client',
                'error_description' => 'Client authentication failed',
            ], 401)
        );

        static::getContainer()->set(TokenManager::class, $tokenManager);

        $client->request(
            'POST',
            '/v1.0/authenticate',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([
                'email' => 'user@example.com',
                'password' => 'password',
            ], JSON_THROW_ON_ERROR),
        );

        self::assertResponseStatusCodeSame(401);
        self::assertSame([
            'error' => 'invalid_client',
            'error_description' => 'Client authentication failed',
        ], json_decode((string) $client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR));
    }
}
