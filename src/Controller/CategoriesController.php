<?php

namespace App\Controller;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends AbstractController
{
    #[Route('/categories/{id}', name: 'app_categories')]
    public function index(int $id, GameRepository $gamesRepository): Response
    {

        $games = $gamesRepository->findBy(['category' => $id]);

        //TODO : ajouter {{ game[0].category.name }} dans le twig

        return $this->render('categories/index.html.twig', [
            'controller_name' => 'CategoriesController',
            'games' => $games,
        ]);
    }
}
