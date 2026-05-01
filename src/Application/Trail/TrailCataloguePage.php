<?php

namespace Application\Trail;

use Trailmind\Trail\Trail;

class TrailCataloguePage
{
    /**
     * @var list<string>
     */
    private const CARD_TONES = ['sand', 'sage', 'stone'];

    /**
     * @var array<string, int>
     */
    private const DIFFICULTY_ORDER = [
        'Easy' => 0,
        'Moderate' => 1,
        'Hard' => 2,
    ];

    /**
     * @param array<int, Trail> $trails
     *
     * @return array{
     *     navigation: list<array{label: string, href: string}>,
     *     heroHighlights: list<array{eyebrow: string, title: string, description: string, delay: string}>,
     *     summaryEntries: list<array{time: string, description: string}>,
     *     trailCards: list<array{eyebrow: string, title: string, description: string, chips: list<string>, tone: string, delay: string}>,
     *     trailCount: int,
     *     outlookNotes: list<string>
     * }
     */
    public function toArray(array $trails): array
    {
        $sortedTrails = $trails;

        usort(
            $sortedTrails,
            static fn (Trail $left, Trail $right): int => strcasecmp($left->getName(), $right->getName())
        );

        return [
            'navigation' => $this->navigation(),
            'heroHighlights' => $this->heroHighlights($sortedTrails),
            'summaryEntries' => $this->summaryEntries($sortedTrails),
            'trailCards' => $this->trailCards($sortedTrails),
            'trailCount' => count($sortedTrails),
            'outlookNotes' => $this->outlookNotes(),
        ];
    }

    /**
     * @return list<array{label: string, href: string}>
     */
    private function navigation(): array
    {
        return [
            [
                'label' => 'Home',
                'href' => '/',
            ],
            [
                'label' => 'Collection',
                'href' => '#collection',
            ],
            [
                'label' => 'Outlook',
                'href' => '#outlook',
            ],
        ];
    }

    /**
     * @param array<int, Trail> $trails
     *
     * @return list<array{eyebrow: string, title: string, description: string, delay: string}>
     */
    private function heroHighlights(array $trails): array
    {
        $trailCount = count($trails);

        return [
            [
                'eyebrow' => 'Collection',
                'title' => $this->formatTrailCount($trailCount),
                'description' => 0 === $trailCount
                    ? 'The catalogue is ready and waiting for its first route to land.'
                    : 'Every current trail record is gathered into one warmer, slower browse.',
                'delay' => '0.08s',
            ],
            [
                'eyebrow' => 'Distance',
                'title' => 0 === $trailCount
                    ? 'Miles to come'
                    : sprintf('%s miles avg', $this->formatLength($this->averageLength($trails))),
                'description' => 'A quick sense of scale, so the list already helps before a map layer arrives.',
                'delay' => '0.16s',
            ],
            [
                'eyebrow' => 'Difficulty',
                'title' => $this->difficultySummary($trails),
                'description' => 'Enough variety for easier starts, steadier outings, and the occasional bigger day.',
                'delay' => '0.24s',
            ],
        ];
    }

    /**
     * @param array<int, Trail> $trails
     *
     * @return list<array{time: string, description: string}>
     */
    private function summaryEntries(array $trails): array
    {
        $trailCount = count($trails);

        return [
            [
                'time' => 'Browse',
                'description' => 0 === $trailCount
                    ? 'The page is ready to receive trails as soon as the database has them.'
                    : sprintf('All %d current trails are laid out here in one calm collection.', $trailCount),
            ],
            [
                'time' => 'Compare',
                'description' => 'Length and difficulty sit right on the surface, so the list stays useful at a glance.',
            ],
            [
                'time' => 'Later',
                'description' => 'This catalogue is already shaped for a future map, richer detail, and filtering without changing tone.',
            ],
        ];
    }

    /**
     * @param array<int, Trail> $trails
     *
     * @return list<array{eyebrow: string, title: string, description: string, chips: list<string>, tone: string, delay: string}>
     */
    private function trailCards(array $trails): array
    {
        $trailCards = [];

        foreach ($trails as $index => $trail) {
            $difficulty = $this->normaliseDifficulty($trail->getDifficulty());
            $trailPointCount = $trail->getTrailPoints()->count();

            $trailCards[] = [
                'eyebrow' => $difficulty,
                'title' => $trail->getName(),
                'description' => $this->describeTrail($trail, $difficulty),
                'chips' => [
                    sprintf('%s miles', $this->formatLength($trail->getLength())),
                    $difficulty,
                    0 === $trailPointCount
                        ? 'Map details soon'
                        : sprintf('%d point%s', $trailPointCount, 1 === $trailPointCount ? '' : 's'),
                ],
                'tone' => self::CARD_TONES[$index % count(self::CARD_TONES)],
                'delay' => sprintf('0.%02ds', 10 + ($index * 8)),
            ];
        }

        return $trailCards;
    }

    /**
     * @return list<string>
     */
    private function outlookNotes(): array
    {
        return [
            'Map browsing can slot in without changing the calmer browsing tone.',
            'Filters and richer trail detail can grow here next.',
        ];
    }

    /**
     * @param array<int, Trail> $trails
     */
    private function averageLength(array $trails): float
    {
        if ([] === $trails) {
            return 0.0;
        }

        $totalLength = array_reduce(
            $trails,
            static fn (float $length, Trail $trail): float => $length + $trail->getLength(),
            0.0
        );

        return $totalLength / count($trails);
    }

    /**
     * @param array<int, Trail> $trails
     */
    private function difficultySummary(array $trails): string
    {
        $difficulties = array_values(array_unique(array_map(
            fn (Trail $trail): string => $this->normaliseDifficulty($trail->getDifficulty()),
            $trails
        )));

        if ([] === $difficulties) {
            return 'Open-ended';
        }

        usort(
            $difficulties,
            fn (string $left, string $right): int => (self::DIFFICULTY_ORDER[$left] ?? 99) <=> (self::DIFFICULTY_ORDER[$right] ?? 99)
        );

        if (1 === count($difficulties)) {
            return $difficulties[0];
        }

        return sprintf('%s to %s', $difficulties[0], $difficulties[count($difficulties) - 1]);
    }

    private function normaliseDifficulty(string $difficulty): string
    {
        return ucfirst(strtolower($difficulty));
    }

    private function formatTrailCount(int $trailCount): string
    {
        return sprintf('%d %s', $trailCount, 1 === $trailCount ? 'trail' : 'trails');
    }

    private function formatLength(float $length): string
    {
        return number_format(round($length + 0.00001, 1), 1);
    }

    private function describeTrail(Trail $trail, string $difficulty): string
    {
        $length = $this->formatLength($trail->getLength());

        return match ($difficulty) {
            'Easy' => sprintf(
                'Around %s miles of lighter going for days when you want scenery without turning the route into work.',
                $length
            ),
            'Moderate' => sprintf(
                'About %s miles of steady trail, with enough substance for a proper outing and room to linger.',
                $length
            ),
            'Hard' => sprintf(
                'A more committed %s-mile route for hikers in the mood for a bigger day outside.',
                $length
            ),
            default => sprintf(
                'A %s-mile trail with a %s profile, ready to become richer once the map layer joins the page.',
                $length,
                strtolower($difficulty)
            ),
        };
    }
}