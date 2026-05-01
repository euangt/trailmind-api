<?php

namespace App\Twig\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'ui:site_header', template: 'components/ui/site_header.html.twig')]
final class SiteHeader
{
    public string $brand = 'Trailmind';

    public string $tagline = 'Casual trail planning for easy weekends';

    /**
     * @var array<int, array{label: string, href: string}>
     */
    public array $items = [];
}