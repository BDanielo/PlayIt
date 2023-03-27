<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AllGamesController extends AbstractController
{
    #[Route('/all/games', name: 'app_all_games')]
    public function index(GameRepository $gamesRepository, CategoryRepository $categoryRepository): Response
    {

        //$games = $gamesRepository->findAll();
        $categories = $categoryRepository->findAll();

        $games = [1,2,3,4,5,6];

        return $this->render('all_games/index.html.twig', [
            'controller_name' => 'AllGamesController',
            'games' => $games,
            'categories' => $categories,
        ]);
    }
}
