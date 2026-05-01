<?php

namespace App\Twig\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'ui:outlook_banner', template: 'components/ui/outlook_banner.html.twig')]
final class OutlookBanner
{
    public string $id = '';

    public string $eyebrow = '';

    public string $title = '';

    public string $description = '';

    /**
     * @var array<int, string>
     */
    public array $notes = [];

    public string $delay = '0s';
}