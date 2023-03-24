<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyPublishedGamesController extends AbstractController
{
    #[Route('/my/published/games', name: 'app_my_published_games')]
    public function index(): Response
    {
        return $this->render('my_published_games/index.html.twig', [
            'controller_name' => 'MyPublishedGamesController',
        ]);
    }
}
