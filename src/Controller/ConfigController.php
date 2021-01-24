<?php
namespace App\Controller;
use App\Entity\Apostasy;
use App\Entity\AppConfig;
use App\Repository\ApostasyRepository;
use App\Repository\AppConfigRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route ("/config")
 * Class
 * @package App\Controller
 */
class ConfigController {

    /**
     * @Route("/", methods={"GET"}, name="get_config")
     * @param AppConfigRepository $appConfigRepository
     * @param ApostasyRepository $apostasyRepository
     * @return JsonResponse
     */
    public function getConfig(AppConfigRepository $appConfigRepository,
                                ApostasyRepository $apostasyRepository): JsonResponse
    {
        $data = $appConfigRepository->getConfigValue(AppConfig::CONFIG_KEYS[AppConfig::START_DATE]);
        $firstApostasyByYear = $apostasyRepository->getFirstApostasy(Apostasy::BY_YEAR);
        $data = [
            'startDate' => $data->getConfigValue(),
            'firstApostasy' => $firstApostasyByYear . '-01-01'
        ];
        return new JsonResponse($data,200);
    }


}