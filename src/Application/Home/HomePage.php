<?php

namespace Application\Home;

class HomePage
{
    /**
     * @return array{
     *     navigation: list<array{label: string, href: string}>,
     *     heroHighlights: list<array{eyebrow: string, title: string, description: string, delay: string}>,
     *     rhythmEntries: list<array{time: string, description: string}>,
     *     trailHighlights: list<array{eyebrow: string, title: string, description: string, chips: list<string>, tone: string, delay: string}>,
     *     valueCards: list<array{title: string, description: string}>,
     *     outlookNotes: list<string>
     * }
     */
    public function toArray(): array
    {
        return [
            'navigation' => $this->navigation(),
            'heroHighlights' => $this->heroHighlights(),
            'rhythmEntries' => $this->rhythmEntries(),
            'trailHighlights' => $this->trailHighlights(),
            'valueCards' => $this->valueCards(),
            'outlookNotes' => $this->outlookNotes(),
        ];
    }

    /**
     * @return list<array{label: string, href: string}>
     */
    public function navigation(): array
    {
        return [
            [
                'label' => 'Trails',
                'href' => '/trails',
            ],
            [
                'label' => 'Highlights',
                'href' => '#highlights',
            ],
            [
                'label' => 'Rhythm',
                'href' => '#rhythm',
            ],
            [
                'label' => 'Outlook',
                'href' => '#outlook',
            ],
        ];
    }

    /**
     * @return list<array{eyebrow: string, title: string, description: string, delay: string}>
     */
    public function heroHighlights(): array
    {
        return [
            [
                'eyebrow' => 'Terrain',
                'title' => 'Woodland to coast',
                'description' => 'Routes that feel close to home but still worth the early start.',
                'delay' => '0.08s',
            ],
            [
                'eyebrow' => 'Vibe',
                'title' => 'Easygoing by design',
                'description' => 'Clear trail notes, comfortable pacing, and room for a slow lunch stop.',
                'delay' => '0.16s',
            ],
            [
                'eyebrow' => 'Focus',
                'title' => 'Weekend-ready',
                'description' => 'Built for casual hikers who still appreciate a route that feels well chosen.',
                'delay' => '0.24s',
            ],
        ];
    }

    /**
     * @return list<array{time: string, description: string}>
     */
    public function rhythmEntries(): array
    {
        return [
            [
                'time' => '08:15',
                'description' => 'Coffee in hand, boots laced, route set.',
            ],
            [
                'time' => '10:40',
                'description' => 'A ridge section worth stopping for, without having to rush through it.',
            ],
            [
                'time' => '13:05',
                'description' => 'Back into town with the good kind of tired and a free afternoon.',
            ],
        ];
    }

    /**
     * @return list<array{eyebrow: string, title: string, description: string, chips: list<string>, tone: string, delay: string}>
     */
    public function trailHighlights(): array
    {
        return [
            [
                'eyebrow' => 'Pine loop',
                'title' => 'Soft climbs, tall shade.',
                'description' => 'A forgiving woodland route with enough elevation to feel earned, but not enough to turn the day into work.',
                'chips' => ['4.8 miles', 'Shaded', 'Easy pace'],
                'tone' => 'sand',
                'delay' => '0.10s',
            ],
            [
                'eyebrow' => 'Coast path',
                'title' => 'Sea air, open views.',
                'description' => 'A breezy stretch for hikers who want the drama of cliffs and sky without overcomplicating the day.',
                'chips' => ['6.1 miles', 'Bright', 'Lunch stop'],
                'tone' => 'sage',
                'delay' => '0.18s',
            ],
            [
                'eyebrow' => 'Moorland',
                'title' => 'Wide ground, slow thoughts.',
                'description' => 'For days when the best part of hiking is the open space, steady footing, and having nowhere urgent to be.',
                'chips' => ['5.4 miles', 'Open views', 'Sunset-ready'],
                'tone' => 'stone',
                'delay' => '0.26s',
            ],
        ];
    }

    /**
     * @return list<array{title: string, description: string}>
     */
    public function valueCards(): array
    {
        return [
            [
                'title' => 'No hard sell',
                'description' => 'Just the first impression of a trail brand that understands slower weekends and good route choices.',
            ],
            [
                'title' => 'Built for growth',
                'description' => 'Ready for internal navigation, richer trail content, and interactive features once you decide to add them.',
            ],
            [
                'title' => 'Symfony-native',
                'description' => 'Rendered with Twig, styled through Tailwind and AssetMapper, and left static on purpose for a clean starting point.',
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public function outlookNotes(): array
    {
        return [
            'Route browsing slots in naturally.',
            'Live interactions can be added later with UX Live Components.',
        ];
    }
}