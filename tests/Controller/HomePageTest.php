<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HomePageTest extends WebTestCase
{
    public function testHomePageLoadsAndRendersExpectedSections(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertPageTitleContains('Trailmind | Easygoing Trail Days');
        self::assertSelectorTextContains('.tm-hero-title', 'A calm front porch for hikers who like their trails scenic, simple, and unhurried.');
        self::assertSelectorTextContains('.tm-timeline-title', 'Plan less. Wander better.');
        self::assertSelectorExists('#highlights');
        self::assertSelectorExists('#rhythm');
        self::assertSelectorExists('#outlook');

        self::assertCount(3, $crawler->filter('.tm-feature-card'));
        self::assertCount(3, $crawler->filter('.tm-trail-card'));
        self::assertCount(3, $crawler->filter('.tm-value-card'));
        self::assertCount(3, $crawler->filter('.tm-timeline-entry'));
    }

    public function testHomePageNavigationAndCallsToActionStayInternal(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        self::assertResponseIsSuccessful();

        $navigationLinks = $crawler->filter('.tm-header nav a')->extract(['href']);
        $ctaLinks = $crawler->filter('.tm-hero-actions a')->extract(['href']);

        self::assertSame(['#highlights', '#rhythm', '#outlook'], $navigationLinks);
        self::assertSame(['#highlights', '#rhythm'], $ctaLinks);
    }
}