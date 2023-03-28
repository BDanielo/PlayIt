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
    public function index(int $id, CategoryRepository $categoryRepository, Request $request): Response
    {
        $categories = $categoryRepository->findAll();
        $selectedCategory = $categoryRepository->find($id)->getName();
        $title = "All $selectedCategory";

        //$games = $gamesRepository->findBy(['category' => $id]);
        $cat = $categoryRepository->findCategoryById($id);
        $games = $cat[0]->getGames();

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            return $this->redirectToRoute('category_search', [
                'id' => $id,
                'input' => $data['input'],
                'rangePrice' => $data['range']
            ]);
        }

        //TODO : ajouter {{ game[0].category.name }} dans le twig

        return $this->render('categories/index.html.twig', [
            'controller_name' => 'CategoriesController',
            'games' => $games,
            'categories' => $categories,
            'title' => $title,
            'form' => $form->createView()
        ]);
    }

    #[Route('/categories/{id}/orderby/{sort}/{order}', name: 'app_sorted_category')]

    public function orderBy(int $id, string $sort, string $order, GameRepository $gameRepository, CategoryRepository $categoryRepository, Request $request): Response
    {
        $categories = $categoryRepository->findAll();
        $selectedCategory = $categoryRepository->find($id)->getName();
        $title = "All $selectedCategory";

        $cat = $categoryRepository->findCategoryById($id);
        $games = $cat[0]->getGames();

        $games = $gameRepository->sortBy($sort, $order, $id);

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            return $this->redirectToRoute('category_search', [
                'id' => $id,
                'input' => $data['input'],
                'rangePrice' => $data['range']
            ]);
        }
        
        return $this->render('categories/index.html.twig', [
            'controller_name' => 'CategoriesController',
            'games' => $games,
            'categories' => $categories,
            'title' => $title,
            'form' => $form->createView()
        ]);

    }

    #[Route('/categories/{id}/search/{input}/{rangePrice}', name: 'category_search')]
    public function search(int $id, string $input, int $rangePrice, GameRepository $gameRepository, CategoryRepository $categoryRepository, Request $request): Response
    {

        $games = $gameRepository->findByName($input, $rangePrice, $id);
        $categories = $categoryRepository->findAll();
        $selectedCategory = $categoryRepository->find($id)->getName();
        $title = "Custom filters on $selectedCategory";

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            return $this->redirectToRoute('category_search', [
                'id' => $id,
                'input' => $data['input'],
                'rangePrice' => $data['range']
            ]);
        }

        return $this->render('categories/index.html.twig', [
            'controller_name' => 'CategoriesController',
            'games' => $games,
            'categories' => $categories,
            'title' => $title,
            'form' => $form->createView()
        ]);
    }
}
