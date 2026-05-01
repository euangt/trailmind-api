<?php

namespace App\Tests\Controller\Trail;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Trailmind\Trail\Trail;
use Trailmind\Trail\TrailRepository;

final class TrailCataloguePageTest extends WebTestCase
{
    public function testTrailCataloguePageLoadsAndRendersTrailCards(): void
    {
        $client = static::createClient();
        $trailRepository = $this->createMock(TrailRepository::class);

        $trailRepository->method('findAll')->willReturn([
            new Trail('Pine Loop', 'Easy', 4.8),
            new Trail('Coast Path', 'Moderate', 6.1),
        ]);

        static::getContainer()->set(TrailRepository::class, $trailRepository);

        $crawler = $client->request('GET', '/trails');

        self::assertResponseIsSuccessful();
        self::assertPageTitleContains('Trailmind | Trail Collection');
        self::assertSelectorTextContains('.tm-hero-title', 'A lovingly kept list of trails for days that deserve a better route.');
        self::assertSelectorTextContains('#collection .tm-section-title', 'Every current route, laid out for an easy browse.');
        self::assertSelectorTextContains('.tm-trail-grid', 'Coast Path');
        self::assertSelectorTextContains('.tm-trail-grid', 'Pine Loop');
        self::assertCount(2, $crawler->filter('.tm-trail-card'));
        self::assertCount(0, $crawler->filter('.tm-notice-panel'));
    }

    public function testTrailCataloguePageRendersEmptyStateWhenNoTrailsExist(): void
    {
        $client = static::createClient();
        $trailRepository = $this->createMock(TrailRepository::class);

        $trailRepository->method('findAll')->willReturn([]);

        static::getContainer()->set(TrailRepository::class, $trailRepository);

        $crawler = $client->request('GET', '/trails');

        self::assertResponseIsSuccessful();
        self::assertCount(0, $crawler->filter('.tm-trail-card'));
        self::assertSelectorTextContains('.tm-notice-title', 'The trail list is waiting for its first route.');
        self::assertSelectorTextContains('.tm-notice-copy', 'Once trails are loaded into the database, they will appear here as a calmer, more considered browsing collection.');
    }
}