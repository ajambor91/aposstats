<?php

namespace App\Service;

use App\Entity\City;
use App\Entity\Voivodeship;

class FitCities
{

    private $cities;
    private $scrappedCity;
    private $fittedCities;
    private $minDistanceCity;

    public function fitCities(): string
    {
        $this->searchCities();
        $this->sortArray();
        return $this->minDistanceCity['name'];
    }

    public function setCities(array $cities): void
    {
        $this->cities = $cities;
    }

    public function setScrappedCity(string $city): void
    {
        $this->scrappedCity = $city;
    }

    private function searchCities(): void
    {
        $result = [];
        /**
         * @var Voivodeship $voivodeship
         */
        foreach ($this->cities as $voivodeshipKey => $voivodeship) {
            /**
             * @var City $city
             */
            foreach ($voivodeship->getCities() as $cityKey => $city) {
                $result[] = [
                    'name' => $city->getName(),
                    'distance' => levenshtein($this->scrappedCity, $city->getName())
                ];
            }
        }
        $this->fittedCities = $result;
    }

    private function sortArray(): void
    {
        foreach ($this->fittedCities as $patternKey => $fittedCity) {
            if (isset($tmpDistance) && $tmpDistance > $fittedCity['distance']) {
                $tmpDistance = $fittedCity['distance'];
                $minDistanceCity = $fittedCity;
            } elseif (!isset($tmpDistance)) {
                $tmpDistance = $fittedCity['distance'];
                $minDistanceCity = $fittedCity;
            }
        }
        $this->minDistanceCity = $minDistanceCity ?: null;
    }
}