<?php

namespace Trailmind\WeatherService;

use Trailmind\WeatherService\Exception\UnableToAcquireWeatherException;

interface WeatherAcquirer
{
    /**
     * @throws UnableToAcquireWeatherException
     */
    public function getWeatherDataForCoordinates(float $longitude, float $latitude): Weather;
}