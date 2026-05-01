<?php

namespace App\Twig\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'ui:value_card', template: 'components/ui/value_card.html.twig')]
final class ValueCard
{
    public string $title = '';

    public string $description = '';
}