<?php

namespace App\Tests\Controller\Api\Trail;

use Dto\Outbound\Success;
use Dto\Outbound\Trail\TrailCollectionBuilder;
use Infrastructure\Oauth2Server\TokenManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Trailmind\Trail\TrailRepository;
use Trailmind\User\User;

final class TrailsControllerTest extends WebTestCase
{
    public function testViewTrailsReturnsSuccessForRoleUser(): void
    {
        $client = static::createClient();
        $tokenManager = $this->createMock(TokenManager::class);
        $trailRepository = $this->createMock(TrailRepository::class);
        $trailCollectionBuilder = $this->createMock(TrailCollectionBuilder::class);
        $success = new Success();

        $success->add('trails', []);

        $tokenManager->method('findUser')->willReturn(
            new User('user@example.com', 'Trail User', 'trail-user', ['ROLE_USER'])
        );
        $trailRepository->method('findAll')->willReturn([]);
        $trailCollectionBuilder->method('setContext')->with('v1.0_view_trails')->willReturn($trailCollectionBuilder);
        $trailCollectionBuilder->method('build')->with([])->willReturn($success);

        static::getContainer()->set(TokenManager::class, $tokenManager);
        static::getContainer()->set(TrailRepository::class, $trailRepository);
        static::getContainer()->set(TrailCollectionBuilder::class, $trailCollectionBuilder);

        $client->request('GET', '/v1.0/trails', server: [
            'HTTP_AUTHORIZATION' => 'Bearer access-token',
        ]);

        self::assertResponseIsSuccessful();
        self::assertSame([
            'trails' => [],
        ], json_decode((string) $client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR));
    }

    public function testViewTrailsReturnsSuccessWhenUserHasMultipleRolesIncludingRequiredRole(): void
    {
        $client = static::createClient();
        $tokenManager = $this->createMock(TokenManager::class);
        $trailRepository = $this->createMock(TrailRepository::class);
        $trailCollectionBuilder = $this->createMock(TrailCollectionBuilder::class);
        $success = new Success();

        $success->add('trails', []);

        $tokenManager->method('findUser')->willReturn(
            new User('user@example.com', 'Trail Manager', 'trail-manager', ['ROLE_ADMIN', 'ROLE_USER'])
        );
        $trailRepository->method('findAll')->willReturn([]);
        $trailCollectionBuilder->method('setContext')->with('v1.0_view_trails')->willReturn($trailCollectionBuilder);
        $trailCollectionBuilder->method('build')->with([])->willReturn($success);

        static::getContainer()->set(TokenManager::class, $tokenManager);
        static::getContainer()->set(TrailRepository::class, $trailRepository);
        static::getContainer()->set(TrailCollectionBuilder::class, $trailCollectionBuilder);

        $client->request('GET', '/v1.0/trails', server: [
            'HTTP_AUTHORIZATION' => 'Bearer access-token',
        ]);

        self::assertResponseIsSuccessful();
        self::assertSame([
            'trails' => [],
        ], json_decode((string) $client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR));
    }

    public function testViewTrailsDeniesAccessWhenUserDoesNotHaveRequiredRole(): void
    {
        $client = static::createClient();
        $tokenManager = $this->createMock(TokenManager::class);
        $trailRepository = $this->createMock(TrailRepository::class);
        $trailCollectionBuilder = $this->createMock(TrailCollectionBuilder::class);

        $tokenManager->method('findUser')->willReturn(
            new User('user@example.com', 'Trail Admin', 'trail-admin', ['ROLE_ADMIN'])
        );

        static::getContainer()->set(TokenManager::class, $tokenManager);
        static::getContainer()->set(TrailRepository::class, $trailRepository);
        static::getContainer()->set(TrailCollectionBuilder::class, $trailCollectionBuilder);

        $client->request('GET', '/v1.0/trails', server: [
            'HTTP_AUTHORIZATION' => 'Bearer access-token',
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    public function testViewTrailsRequiresAuthenticationToken(): void
    {
        $client = static::createClient();

        $client->request('GET', '/v1.0/trails');

        self::assertResponseStatusCodeSame(401);
        self::assertSame([
            'error' => 'authentication_required',
            'error_description' => 'Authentication token required',
        ], json_decode((string) $client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR));
    }
}
