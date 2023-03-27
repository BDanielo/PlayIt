<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AllGamesController extends AbstractController
{
    #[Route('/all/games', name: 'app_all_games')]
    public function index(GameRepository $gamesRepository, CategoryRepository $categoryRepository): Response
    {

        $games = $gamesRepository->findAll();
        $categories = $categoryRepository->findAll();

        return $this->render('all_games/index.html.twig', [
            'controller_name' => 'AllGamesController',
            'games' => $games,
            'categories' => $categories,
        ]);
    }

    #[Route('/all/games/orderby/{filter}', name: 'app_ordered_games', methods: ['GET'])]

    public function orderBy(string $filter, GameRepository $gameRepository, CategoryRepository $categoryRepository, Request $request): Response
    {
        $categories = $categoryRepository->findAll();
        $request->query->get('filter');
        
        if( $filter == 'sells') {
            $games = $gameRepository->orderBySells();
        }
        if ( $filter == 'date') {
            $games = $gameRepository->orderByDate();
        }
        if ( $filter == 'price') {
            $games = $gameRepository->orderByPrice();
        }
        
        return $this->render('all_games/index.html.twig', [
            'controller_name' => 'AllGamesController',
            'games' => $games,
            'categories' => $categories,
        ]);

    }
}
