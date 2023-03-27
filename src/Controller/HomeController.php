<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(GameRepository $gamesRepository, CategoryRepository $categoryRepository): Response
    {
        $games = $gamesRepository->findAll();
        $categories = $categoryRepository->findAll();

        //TODO: add sales to games table

        $sales = $games;

        $populars = $gamesRepository->getBestSells(3);

        $slider = [];
        $randoms = [];

        for ($i = 0; $i < 5 && $i< count($games) ; $i++) {
            //$index = rand(0, count($games) - 1);
            $index = rand(0, count($games) - 1);
            if ($i != 0) {
                while (in_array($index, $randoms)) {
                    $index = rand(0, count($games) - 1);
                }
            }
            $randoms[] = $index;
            $slider[] = $games[$index];
        }
        
        

        return $this->render('home.html.twig', [
            'controller_name' => 'HomeController',
            'sales' => $sales,
            'populars' => $populars,
            'categories' => $categories,
            'slider' => $slider
        ]);
    }
}
