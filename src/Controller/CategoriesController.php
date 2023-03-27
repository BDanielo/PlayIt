<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\CategoryRepository;
use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends AbstractController
{
    #[Route('/categories/{id}', name: 'app_categories')]
    public function index(int $id, GameRepository $gamesRepository, CategoryRepository $categoryRepository, Request $request): Response
    {
        $categories = $categoryRepository->findAll();
        $catName = $categoryRepository->find($id)->getName();

        //$games = $gamesRepository->findBy(['category' => $id]);
        $cat = $categoryRepository->findCategoryById($id);
        $games = $cat[0]->getGames();

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            return $this->redirectToRoute('app_search', [
                'input' => $data['input']
            ]);
        }

        //TODO : ajouter {{ game[0].category.name }} dans le twig

        return $this->render('categories/index.html.twig', [
            'controller_name' => 'CategoriesController',
            'games' => $games,
            'categories' => $categories,
            'catName' => $catName,
            'form' => $form->createView()
        ]);
    }
}
