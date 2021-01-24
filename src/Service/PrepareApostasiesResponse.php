<?php

namespace App\Service;

use App\Entity\Apostasy;

class PrepareApostasiesResponse
{
    public function prepareData(array $data): array
    {
        $result = [];
        /**
         * @var Apostasy $datum
         */
        foreach ($data as $datum) {
            $result[] = [
                'year' => $datum->getApostasyYear(),
                'scrappedAt' => $datum->getScrappedAt()->getTimestamp(),
                'city' => $datum->getFittedCity()->getName(),
                'cityId' => $datum->getFittedCity()->getId(),
                'voivodeship' => $datum->getFittedVoivdeship()->getName(),
                'voivodeshipId' => $datum->getFittedVoivdeship()->getId(),
                'ordinalNumber' => $datum->getOrdinalNumber()
            ];
        }
        return $result;
    }

    public function prepareStats($data): array {
        return [[
            'name' => 'Apostazje',
            'series' => $data
        ]];
    }
}