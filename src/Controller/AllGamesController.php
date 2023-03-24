<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AllGamesController extends AbstractController
{
    #[Route('/all/games', name: 'app_all_games')]
    public function index(): Response
    {
        return $this->render('all_games/index.html.twig', [
            'controller_name' => 'AllGamesController',
        ]);
    }
}
