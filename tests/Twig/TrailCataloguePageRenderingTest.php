<?php

namespace App\Tests\Twig;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Twig\Environment;

final class TrailCataloguePageRenderingTest extends KernelTestCase
{
    public function testTrailCataloguePageRendersOverviewAndTrailCards(): void
    {
        self::bootKernel();

        $twig = self::getContainer()->get(Environment::class);
        $html = $twig->render('trails/index.html.twig', [
            'navigation' => [
                ['label' => 'Home', 'href' => '/'],
                ['label' => 'Collection', 'href' => '#collection'],
                ['label' => 'Outlook', 'href' => '#outlook'],
            ],
            'heroHighlights' => [
                [
                    'eyebrow' => 'Collection',
                    'title' => '2 trails',
                    'description' => 'Every current trail record is gathered into one warmer, slower browse.',
                    'delay' => '0.08s',
                ],
            ],
            'summaryEntries' => [
                [
                    'time' => 'Browse',
                    'description' => 'All current trails are laid out here in one calm collection.',
                ],
            ],
            'trailCards' => [
                [
                    'eyebrow' => 'Moderate',
                    'title' => 'Coast Path',
                    'description' => 'About 6.1 miles of steady trail, with enough substance for a proper outing and room to linger.',
                    'chips' => ['6.1 miles', 'Moderate', 'Map details soon'],
                    'tone' => 'sand',
                    'delay' => '0.10s',
                ],
            ],
            'trailCount' => 2,
            'outlookNotes' => [
                'Map browsing can slot in without changing the calmer browsing tone.',
                'Filters and richer trail detail can grow here next.',
            ],
        ]);

        self::assertStringContainsString('A lovingly kept list of trails for days that deserve a better route.', $html);
        self::assertStringContainsString('Coast Path', $html);
        self::assertStringContainsString('href="#collection"', $html);
        self::assertStringContainsString('This catalogue is designed to grow into a map-first trail finder.', $html);
        self::assertStringContainsString('Every current route, laid out for an easy browse.', $html);
        self::assertStringNotContainsString('The trail list is waiting for its first route.', $html);
    }
}