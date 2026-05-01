<?php

namespace App\Twig\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'ui:feature_card', template: 'components/ui/feature_card.html.twig')]
final class FeatureCard
{
    public string $eyebrow = '';

    public string $title = '';

    public string $description = '';

    public string $delay = '0s';
}