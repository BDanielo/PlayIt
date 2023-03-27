<?php

namespace App\Controller;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AllGamesController extends AbstractController
{
    #[Route('/all-games', name: 'app_all_games')]
    public function index(GameRepository $gameRepository): Response
    {
        // get all games
        $games = $gameRepository->findAll();

        return $this->render('all_games/index.html.twig', [
            'controller_name' => 'AllGamesController',
            'games' => $games,
        ]);
    }
}
