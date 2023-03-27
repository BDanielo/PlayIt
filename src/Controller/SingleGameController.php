<?php

namespace App\Controller;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SingleGameController extends AbstractController
{
    #[Route('/single/game/{id}', name: 'app_single_game')]
    public function index(int $id, GameRepository $gameRepository): Response
    {

        $game = $gameRepository->find($id);

        return $this->render('single_game/index.html.twig', [
            'controller_name' => 'SingleGameController',
            'game' => $game
        ]);
    }
}
