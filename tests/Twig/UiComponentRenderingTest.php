<?php

namespace App\Tests\Twig;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Twig\Environment;

final class UiComponentRenderingTest extends KernelTestCase
{
    public function testTrailCardComponentRendersToneAndChips(): void
    {
        self::bootKernel();

        $twig = self::getContainer()->get(Environment::class);
        $html = $twig->createTemplate(<<<'TWIG'
            {{ component('ui:trail_card', {
                eyebrow: 'Pine loop',
                title: 'Soft climbs, tall shade.',
                description: 'A forgiving woodland route.',
                chips: ['4.8 miles', 'Shaded'],
                tone: 'sand',
                delay: '0.10s'
            }) }}
        TWIG)->render();

        self::assertStringContainsString('tm-trail-card--sand', $html);
        self::assertStringContainsString('Pine loop', $html);
        self::assertStringContainsString('4.8 miles', $html);
        self::assertStringContainsString('Shaded', $html);
    }

    public function testSiteHeaderComponentRendersSuppliedNavigationItems(): void
    {
        self::bootKernel();

        $twig = self::getContainer()->get(Environment::class);
        $html = $twig->createTemplate(<<<'TWIG'
            {{ component('ui:site_header', {
                brand: 'Trailmind',
                tagline: 'Casual trail planning for easy weekends',
                items: [
                    { label: 'Highlights', href: '#highlights' },
                    { label: 'Rhythm', href: '#rhythm' }
                ]
            }) }}
        TWIG)->render();

        self::assertStringContainsString('Trailmind', $html);
        self::assertStringContainsString('Casual trail planning for easy weekends', $html);
        self::assertStringContainsString('href="#highlights"', $html);
        self::assertStringContainsString('href="#rhythm"', $html);
    }
}