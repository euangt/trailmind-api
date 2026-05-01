<?php

namespace App\Twig\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'ui:timeline_panel', template: 'components/ui/timeline_panel.html.twig')]
final class TimelinePanel
{
    public string $eyebrow = '';

    public string $title = '';

    /**
     * @var array<int, array{time: string, description: string}>
     */
    public array $entries = [];

    public string $summary = '';

    public string $delay = '0s';
}