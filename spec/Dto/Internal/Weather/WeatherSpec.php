<?php

namespace spec\Dto\Internal\Weather;

use PhpSpec\ObjectBehavior;

class WeatherSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            '2024-06-01 12:00',
            20.5,
            19.0,
            18.5,
            10.0,
            0.0,
            65,
            'Sunny'
        );
    }

    function it_has_a_last_updated()
    {
        $this->last_updated->shouldBe('2024-06-01 12:00');
    }

    function it_has_a_temp_c()
    {
        $this->temp_c->shouldBe(20.5);
    }

    function it_has_a_feelslike_c()
    {
        $this->feelslike_c->shouldBe(19.0);
    }

    function it_has_a_windchill_c()
    {
        $this->windchill_c->shouldBe(18.5);
    }

    function it_has_a_wind_mph()
    {
        $this->wind_mph->shouldBe(10.0);
    }

    function it_has_a_precip_mm()
    {
        $this->precip_mm->shouldBe(0.0);
    }

    function it_has_a_humidity()
    {
        $this->humidity->shouldBe(65);
    }

    function it_has_a_condition()
    {
        $this->condition->shouldBe('Sunny');
    }
}