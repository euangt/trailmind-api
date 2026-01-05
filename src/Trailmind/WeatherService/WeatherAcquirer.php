<?php

namespace Trailmind\WeatherService;

use Dto\Internal\Weather\Weather;
use Trailmind\WeatherService\Exception\UnableToAcquireWeatherException;

interface WeatherAcquirer
{
    /**
     * @param float $longitude
     * @param float $latitude
     * 
     * @throws UnableToAcquireWeatherException
     */
    public function getWeatherDataForCoordinates(float $longitude, float $latitude): Weather;
}