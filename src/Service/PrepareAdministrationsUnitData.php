<?php

namespace App\Service;

use App\Entity\City;
use App\Entity\Voivodeship;

class PrepareAdministrationsUnitData
{
    public function prepareCities(array $data): array
    {
        $result = [];
        /**
         * @var City $datum
         */
        foreach ($data as $datum) {
            $existingApostasy = $datum->getApostasies()->toArray();
            if(empty($existingApostasy)) {
                continue;
            }
            $result[] = [
                'id' => $datum->getId(),
                'name' => $datum->getName()
            ];
        }
        return $result;
    }

    public function prepareVoivodeships(array $data): array
    {
        $result = [];
        /**
         * @var Voivodeship $datum
         */
        foreach ($data as $datum) {
            $result[] = [
                'id' => $datum->getId(),
                'name' => ucfirst(mb_strtolower($datum->getName()))
            ];
        }
        return $result;
    }
}