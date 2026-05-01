<?php

namespace Trailmind\WeatherService;

use Symfony\Component\Validator\Constraints as Assert;

class Weather
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $last_updated,
        #[Assert\NotBlank]
        public readonly float $temp_c,
        #[Assert\NotBlank]
        public readonly float $feelslike_c,
        #[Assert\NotBlank]
        public readonly float $windchill_c,
        #[Assert\NotBlank]
        public readonly float $wind_mph,
        #[Assert\NotBlank]
        public readonly float $precip_mm,
        #[Assert\NotBlank]
        public readonly int $humidity,
        #[Assert\NotBlank]
        public readonly string $condition,
    ) {}
}