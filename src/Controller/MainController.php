<?php
namespace App\Controller;

use App\Service\Scrapper;
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
    public function showIndex(Scrapper $scrapper): JsonResponse {
        $page = $scrapper->fillDatabase();
        return new JsonResponse('ok', 200);
    }

}