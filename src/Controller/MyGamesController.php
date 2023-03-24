<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyGamesController extends AbstractController
{
    #[Route('/user/my-games', name: 'app_my_games')]
    public function index(): Response
    {
        return $this->render('my_games/index.html.twig', [
            'controller_name' => 'MyGamesController',
        ]);
    }
}
