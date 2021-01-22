<?php

namespace App\Controller;

use App\Entity\Apostasy;
use App\Entity\AppConfig;
use App\Repository\ApostasyRepository;
use App\Repository\AppConfigRepository;
use App\Repository\CityRepository;
use App\Repository\VoivodeshipRepository;
use App\Service\PrepareAdministrationsUnitData;
use App\Service\PrepareApostasiesResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class MainController extends AbstractController
{

    /**
     * @Route("/", methods={"GET"}, name="main_page")
     * @param Request $request
     * @param ApostasyRepository $apostasyRepository
     * @param PrepareApostasiesResponse $apostasiesResponse
     * @param AppConfigRepository $configRepository
     * @return JsonResponse
     * @throws \Exception
     */

    public function showIndex(Request $request,
                              ApostasyRepository $apostasyRepository,
                              PrepareApostasiesResponse $apostasiesResponse,
                              AppConfigRepository $configRepository): JsonResponse
    {

        $data = $request->query->all();
        if (isset($data['period']) && $data['period'] === Apostasy::BY_MONTH) {
            $startDate = $configRepository->findOneBy(['configKey' => AppConfig::CONFIG_KEYS[AppConfig::START_DATE]]);
            if (new \DateTime($data['from']) < new \DateTime($startDate)) {
                $data['from'] = $startDate;
            }
        }
        $result = $apostasyRepository->getApostasiesData($data);
        $result = $apostasiesResponse->prepareData($result);
        return new JsonResponse($result, 200);
    }

    /**
     * @Route("/get-available-voivodeships", methods={"GET"}, name="get_available_voivodeships")
     * @param VoivodeshipRepository $voivodeshipRepository
     * @param PrepareAdministrationsUnitData $administrationsUnitData
     * @return JsonResponse
     */
    public function getAvailableVoivodeships(VoivodeshipRepository $voivodeshipRepository,
                                             PrepareAdministrationsUnitData $administrationsUnitData): JsonResponse
    {
        $data = $voivodeshipRepository->findAll();
        $data = $administrationsUnitData->prepareVoivodeships($data);
        return new JsonResponse($data, 200);
    }

    /**
     * @Route ("/get-available-cities", methods={"GET"}, name="get_available_cities")
     * @param Request $request
     * @param CityRepository $cityRepository
     * @param PrepareAdministrationsUnitData $administrationsUnitData
     * @return JsonResponse
     */
    public function getAvailableCities(Request $request,
                                       CityRepository $cityRepository,
                                       PrepareAdministrationsUnitData $administrationsUnitData): JsonResponse
    {
        $voivodeship = $request->query->get('voivodeshipId');
        $data = $cityRepository->findBy(['voivodeship' => $voivodeship]);
        $data = $administrationsUnitData->prepareCities($data);
        return new JsonResponse($data, 200);
    }

}