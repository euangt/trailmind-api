<?php

namespace App\Twig\Components\Ui;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'ui:notice_panel', template: 'components/ui/notice_panel.html.twig')]
final class NoticePanel
{
    public string $eyebrow = '';

    public string $title = '';

    public string $description = '';

    public string $delay = '0s';

    public string $className = '';
}