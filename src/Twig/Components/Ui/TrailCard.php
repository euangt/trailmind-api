<?php

namespace App\Twig\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'ui:trail_card', template: 'components/ui/trail_card.html.twig')]
final class TrailCard
{
    public string $eyebrow = '';

    public string $title = '';

    public string $description = '';

    /**
     * @var array<int, string>
     */
    public array $chips = [];

    public string $tone = 'sand';

    public string $delay = '0s';
}