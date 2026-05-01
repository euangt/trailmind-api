<?php

namespace spec\Application\Trail;

use Application\Trail\TrailCataloguePage;
use PhpSpec\ObjectBehavior;
use Trailmind\Trail\Trail;

class TrailCataloguePageSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TrailCataloguePage::class);
    }

    function it_builds_curated_template_data_from_trails()
    {
        $pineLoop = new Trail('Pine Loop', 'Easy', 4.8);
        $coastPath = new Trail('Coast Path', 'Moderate', 6.1);

        $this->toArray([$pineLoop, $coastPath])->shouldReturn([
            'navigation' => [
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
            ],
            'heroHighlights' => [
                [
                    'eyebrow' => 'Collection',
                    'title' => '2 trails',
                    'description' => 'Every current trail record is gathered into one warmer, slower browse.',
                    'delay' => '0.08s',
                ],
                [
                    'eyebrow' => 'Distance',
                    'title' => '5.5 miles avg',
                    'description' => 'A quick sense of scale, so the list already helps before a map layer arrives.',
                    'delay' => '0.16s',
                ],
                [
                    'eyebrow' => 'Difficulty',
                    'title' => 'Easy to Moderate',
                    'description' => 'Enough variety for easier starts, steadier outings, and the occasional bigger day.',
                    'delay' => '0.24s',
                ],
            ],
            'summaryEntries' => [
                [
                    'time' => 'Browse',
                    'description' => 'All 2 current trails are laid out here in one calm collection.',
                ],
                [
                    'time' => 'Compare',
                    'description' => 'Length and difficulty sit right on the surface, so the list stays useful at a glance.',
                ],
                [
                    'time' => 'Later',
                    'description' => 'This catalogue is already shaped for a future map, richer detail, and filtering without changing tone.',
                ],
            ],
            'trailCards' => [
                [
                    'eyebrow' => 'Moderate',
                    'title' => 'Coast Path',
                    'description' => 'About 6.1 miles of steady trail, with enough substance for a proper outing and room to linger.',
                    'chips' => ['6.1 miles', 'Moderate', 'Map details soon'],
                    'tone' => 'sand',
                    'delay' => '0.10s',
                ],
                [
                    'eyebrow' => 'Easy',
                    'title' => 'Pine Loop',
                    'description' => 'Around 4.8 miles of lighter going for days when you want scenery without turning the route into work.',
                    'chips' => ['4.8 miles', 'Easy', 'Map details soon'],
                    'tone' => 'sage',
                    'delay' => '0.18s',
                ],
            ],
            'trailCount' => 2,
            'outlookNotes' => [
                'Map browsing can slot in without changing the calmer browsing tone.',
                'Filters and richer trail detail can grow here next.',
            ],
        ]);
    }

    function it_handles_an_empty_trail_collection()
    {
        $this->toArray([])->shouldReturn([
            'navigation' => [
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
            ],
            'heroHighlights' => [
                [
                    'eyebrow' => 'Collection',
                    'title' => '0 trails',
                    'description' => 'The catalogue is ready and waiting for its first route to land.',
                    'delay' => '0.08s',
                ],
                [
                    'eyebrow' => 'Distance',
                    'title' => 'Miles to come',
                    'description' => 'A quick sense of scale, so the list already helps before a map layer arrives.',
                    'delay' => '0.16s',
                ],
                [
                    'eyebrow' => 'Difficulty',
                    'title' => 'Open-ended',
                    'description' => 'Enough variety for easier starts, steadier outings, and the occasional bigger day.',
                    'delay' => '0.24s',
                ],
            ],
            'summaryEntries' => [
                [
                    'time' => 'Browse',
                    'description' => 'The page is ready to receive trails as soon as the database has them.',
                ],
                [
                    'time' => 'Compare',
                    'description' => 'Length and difficulty sit right on the surface, so the list stays useful at a glance.',
                ],
                [
                    'time' => 'Later',
                    'description' => 'This catalogue is already shaped for a future map, richer detail, and filtering without changing tone.',
                ],
            ],
            'trailCards' => [],
            'trailCount' => 0,
            'outlookNotes' => [
                'Map browsing can slot in without changing the calmer browsing tone.',
                'Filters and richer trail detail can grow here next.',
            ],
        ]);
    }
}