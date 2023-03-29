<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\GameRepository;
use App\Services\GameReviewService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(GameRepository $gamesRepository, CategoryRepository $categoryRepository, GameReviewService $gameReviewService): Response
    {
        $games = $gamesRepository->findAll();
        $categories = $categoryRepository->findAll();

        $randomsNbr = [];

        for ($i = 0; $i < 5 && $i < count($categories); $i++) {
            $index = rand(0, count($categories) - 1);
            if ($i != 0) {
                while (in_array($index, $randomsNbr)) {
                    $index = rand(0, count($categories) - 1);
                }
            }
            $randomsNbr[] = $index;
            $featuredCategories[] = $categories[$index];
        }

        //TODO: add sales to games table

        $sales = $games;

        $populars = $gamesRepository->getBestSells(3);

        $slider = [];
        $randoms = [];

        for ($i = 0; $i < 5 && $i < count($games); $i++) {
            $index = rand(0, count($games) - 1);
            if ($i != 0) {
                while (in_array($index, $randoms)) {
                    $index = rand(0, count($games) - 1);
                }
            }
            $randoms[] = $index;
            $slider[] = $games[$index];
        }

        $avgRatings = [];
        $starsNbr = [];

        foreach ($populars as $popular) {
            $avgRatings[$popular->getId()] = $gameReviewService->getAvgReview($popular);
            $starsNbr[$popular->getId()] = round($avgRatings[$popular->getId()][0], 1);
            $avgRatings[$popular->getId()][0] = number_format($avgRatings[$popular->getId()][0], 1, '.', '');
        }

        foreach ($sales as $sale) {
            $avgRatings[$sale->getId()] = $gameReviewService->getAvgReview($sale);
            $starsNbr[$sale->getId()] = round($avgRatings[$sale->getId()][0], 1);
            $avgRatings[$sale->getId()][0] = number_format($avgRatings[$sale->getId()][0], 1, '.', '');
        }



        return $this->render('home.html.twig', [
            'controller_name' => 'HomeController',
            'sales' => $sales,
            'populars' => $populars,
            'categories' => $categories,
            'avgRatings' => $avgRatings,
            'starsNbr' => $starsNbr,
            'featuredCategories' => $featuredCategories,
            'slider' => $slider
        ]);
    }
}
