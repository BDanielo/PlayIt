<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\CategoryRepository;
use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AllGamesController extends AbstractController
{
    #[Route('/all/games', name: 'app_all_games')]
    public function index(GameRepository $gamesRepository, CategoryRepository $categoryRepository, Request $request): Response
    {

        $games = $gamesRepository->findAll();
        $categories = $categoryRepository->findAll();

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            return $this->redirectToRoute('app_search', [
                'input' => $data['input']
            ]);
        }

        return $this->render('all_games/index.html.twig', [
            'controller_name' => 'AllGamesController',
            'games' => $games,
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }

    #[Route('/all/games/orderby/{sort}/{order}', name: 'app_sorted_games')]

    public function orderBy(string $sort, string $order, GameRepository $gameRepository, CategoryRepository $categoryRepository, Request $request): Response
    {
        $categories = $categoryRepository->findAll();

        $games = $gameRepository->sortBy($sort, $order, null);

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            return $this->redirectToRoute('app_search', [
                'input' => $data['input']
            ]);
        }
        
        return $this->render('all_games/index.html.twig', [
            'controller_name' => 'AllGamesController',
            'games' => $games,
            'categories' => $categories,
            'form' => $form->createView()
        ]);

    }

    #[Route('/all/games/search/{input}', name: 'app_search')]

    public function search(string $input, GameRepository $gameRepository, CategoryRepository $categoryRepository, Request $request): Response
    {

        $games = $gameRepository->findByName($input);
        $categories = $categoryRepository->findAll();

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            return $this->redirectToRoute('app_search', [
                'input' => $data['input']
            ]);
        }

        return $this->render('all_games/index.html.twig', [
            'controller_name' => 'AllGamesController',
            'games' => $games,
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }
}
