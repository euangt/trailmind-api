<?php

namespace App\Twig\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'ui:section_intro', template: 'components/ui/section_intro.html.twig')]
final class SectionIntro
{
    public string $eyebrow = '';

    public string $title = '';

    public string $description = '';

    public string $delay = '0s';
}