<?php

namespace App\Service;

use mrcnpdlk\Teryt\NativeApi;
use mrcnpdlk\Teryt\Config;
use mrcnpdlk\Teryt\ResponseModel\Territory\JednostkaTerytorialna;

class Teryt
{
    /**
     * @var NativeApi $terytAPI
     */
    private $terytAPI;
    private $data;

    public function getTerytData(): array
    {
        $this->loginAndGetAPI();
        $this->getVoivodeships();
        $this->getCounties();
        $this->getCommunities();
        return $this->data;
    }

    private function loginAndGetAPI(): void
    {
        $config = new Config([
            'username' => $_ENV['TERYT_LOGIN'],
            'password' => $_ENV['TERYT_PASSWORD'],
            'isProduction' => true
        ]);
        $this->terytAPI = NativeApi::create($config);
    }

    private function getVoivodeships(): void
    {
        $this->data = $this->terytAPI->PobierzListeWojewodztw();
    }

    private function getCounties(): void
    {
        $voivodeships = $this->data;
        $this->data = [];
        /**
         * @var JednostkaTerytorialna $voivodeship
         */
        foreach ($voivodeships as $voivodeship) {
            $this->data[$voivodeship->provinceId]['id'] = $voivodeship->provinceId;
            $this->data[$voivodeship->provinceId]['name'] = $voivodeship->name;
            $this->data[$voivodeship->provinceId]['status'] = $voivodeship->typeName;
            $this->data[$voivodeship->provinceId]['counties'] = $this->terytAPI->PobierzListePowiatow($voivodeship->provinceId);
        }
    }

    private function getCommunities(): void
    {
        $voivodeships = $this->data;
        foreach ($voivodeships as $voivodeship) {
            /**
             * @var JednostkaTerytorialna $item
             */
            unset($this->data[$voivodeship['id']]['counties']);
            foreach ($voivodeship['counties'] as $item) {
                $this->data[$voivodeship['id']]['counties'][$item->districtId]['name'] = $item->name;
                $this->data[$voivodeship['id']]['counties'][$item->districtId]['id'] = $item->districtId;
                $this->data[$voivodeship['id']]['counties'][$item->districtId]['communities'] = $this->terytAPI->PobierzListeGmin($voivodeship['id'], $item->districtId);
            }
        }
    }
}