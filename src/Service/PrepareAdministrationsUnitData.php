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
        return $this->alphaSort($result);
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
                'name' => mb_convert_case(ucfirst(mb_strtolower($datum->getName())), MB_CASE_TITLE, 'UTF-8')
            ];
        }
        return $this->alphaSort($result);
    }

    private function alphaSort(array $data): array
    {
        usort($data, function ($item1, $item2) {
            return $item1['name'] <=> $item2['name'];
        });
        return $data;
    }
}