<?php
namespace App\Controller;

use App\Repository\ApostasyRepository;
use App\Repository\VoivodeshipRepository;
use App\Service\FitCities;
use App\Service\MergeTerytData;
use App\Service\Scrapper;
use App\Service\Teryt;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/")
 */
class MainController extends AbstractController {
    /**
     * @Route("/", methods={"GET"}, name="main_page")
     * @param Scrapper $scrapper
     * @return JsonResponse
     */

    public function showIndex(MergeTerytData $scrapper, Teryt $teryt,VoivodeshipRepository  $voivodeshipRepository, ApostasyRepository $apostasyRepository,FitCities $fitCities): JsonResponse {
//        $teryt->getTerytData();
//        $page = $scrapper->mergeData();
//        $voivodeshipRepository->insertData($page);
        $cities =  $voivodeshipRepository->findAll();
        $fitCities->setCities($cities);
        $fitCities->setScrappedCity('Kawtowcei');
        $city = $fitCities->fitCities();
        dump($city);die;
        return new JsonResponse('ok', 200);
    }

}