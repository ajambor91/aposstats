<?php
namespace App\Controller;

use App\Repository\VoivodeshipRepository;
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

    public function showIndex(MergeTerytData $scrapper, Teryt $teryt,VoivodeshipRepository  $voivodeshipRepository): JsonResponse {
//        $teryt->getTerytData();
        $page = $scrapper->mergeData();
        $voivodeshipRepository->insertData($page);
        return new JsonResponse('ok', 200);
    }

}