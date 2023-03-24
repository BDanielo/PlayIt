<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\GamesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(GamesRepository $gamesRepository, CategoryRepository $categoryRepository): Response
    {
        $games = $gamesRepository->findAll();
        $categories = $categoryRepository->findAll();

        $sales = [1,2,3];
        $populars = [1,2,3];

        return $this->render('home.html.twig', [
            'controller_name' => 'HomeController',
            'sales' => $sales,
            'populars' => $populars,
            'categories' => $categories,
        ]);
    }
}
