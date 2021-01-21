<?php
namespace App\Service;
class MergeTerytData
{
    private $array;

    public function mergeData(array $data): array
    {
        $this->array = $data;
        $this->serializeDataTMP();
        $this->removeDuplicateValues();
        return $this->array;
    }

    private function serializeDataTMP(): void
    {
        $voivodeships = [];
        $array = $this->array;
        $this->array = null;
        foreach ($array as $voivodeship) {
            $voivodeships[$voivodeship['id']]['name'] = $voivodeship['name'];
            foreach ($voivodeship['counties'] as $county) {
                foreach ($county['communities'] as $community) {
                    $voivodeships[$voivodeship['id']]['cities'][] = $community->name;
                }
            }
        }
        $this->array = $voivodeships;
    }

    private function removeDuplicateValues(): void
    {
        $result = [];
        $data = $this->array;
        foreach ($data as $key => $datum) {
            $array = array_unique((array)$datum['cities']);
            $result[$key]['voivodeship'] = $datum['name'];
            $result[$key]['cities'] = array_values($array);
        }
        $this->array = array_values($result);
    }
}