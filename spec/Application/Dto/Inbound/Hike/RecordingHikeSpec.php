<?php

namespace spec\Application\Dto\Inbound\Hike;

use Application\Dto\Inbound\Hike\RecordingHike;
use PhpSpec\ObjectBehavior;

class RecordingHikeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('trail-uuid-1234', '2024-06-01 08:00:00', '2024-06-01 16:00:00');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RecordingHike::class);
    }

    function it_should_hold_the_trail_id()
    {
        $this->trailId->shouldBe('trail-uuid-1234');
    }

    function it_should_hold_the_start_date()
    {
        $this->startDate->shouldBe('2024-06-01 08:00:00');
    }

    function it_should_hold_the_end_date()
    {
        $this->endDate->shouldBe('2024-06-01 16:00:00');
    }
}
