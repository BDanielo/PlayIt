<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\CategoryRepository;
use App\Repository\GameRepository;
use App\Services\GameReviewService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends AbstractController
{
    #[Route('/categories/{id}', name: 'app_categories')]
    public function index(int $id, CategoryRepository $categoryRepository, Request $request, GameReviewService $gameReviewService): Response
    {
        $categories = $categoryRepository->findAll();
        $selectedCategory = $categoryRepository->find($id)->getName();
        $title = "All $selectedCategory";

        //$games = $gamesRepository->findBy(['category' => $id]);
        $cat = $categoryRepository->findCategoryById($id);
        $games = $cat[0]->getGames();

        $isPromoted = [];

        $avgRatings = [];
        $starsNbr = [];

        foreach ($games as $game) {
            $avgRatings[$game->getId()] = $gameReviewService->getAvgReview($game);
            $starsNbr[$game->getId()] = round($avgRatings[$game->getId()][0], 1);
            $avgRatings[$game->getId()][0] = number_format($avgRatings[$game->getId()][0], 1, '.', '');

            if ($game->getPromotion() !== null) {
                $now = new \DateTime('now');
                if ($game->getPromotionStart() <= $now && $game->getPromotionEnd() >= $now) {
                    $isPromoted[$game->getId()] = true;
                } else {
                    $isPromoted[$game->getId()] = false;
                }
            } else {
                $isPromoted[$game->getId()] = false;
            }
        }

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            if ($data['promotions'] == '') {
                $promo = 0;
            } else {
                $promo = 1;
            }

            return $this->redirectToRoute('category_search', [
                'id' => $id,
                'input' => $data['input'],
                'rangePrice' => $data['range'],
                'promotions' => $promo
            ]);
        }

        //TODO : ajouter {{ game[0].category.name }} dans le twig

        return $this->render('categories/index.html.twig', [
            'controller_name' => 'CategoriesController',
            'games' => $games,
            'categories' => $categories,
            'title' => $title,
            'avgRatings' => $avgRatings,
            'starsNbr' => $starsNbr,
            'isPromoted' => $isPromoted,
            'form' => $form->createView()
        ]);
    }

    #[Route('/categories/{id}/orderby/{sort}/{order}', name: 'app_sorted_category')]

    public function orderBy(int $id, string $sort, string $order, GameRepository $gameRepository, CategoryRepository $categoryRepository, Request $request, GameReviewService $gameReviewService): Response
    {
        $categories = $categoryRepository->findAll();
        $selectedCategory = $categoryRepository->find($id)->getName();
        $title = "All $selectedCategory";

        $cat = $categoryRepository->findCategoryById($id);
        $games = $cat[0]->getGames();

        $isPromoted = [];

        foreach ($games as $game) {
            $avgRatings[$game->getId()] = $gameReviewService->getAvgReview($game);
            $starsNbr[$game->getId()] = round($avgRatings[$game->getId()][0], 1);
            $avgRatings[$game->getId()][0] = number_format($avgRatings[$game->getId()][0], 1, '.', '');

            if ($game->getPromotion() !== null) {
                $now = new \DateTime('now');
                if ($game->getPromotionStart() <= $now && $game->getPromotionEnd() >= $now) {
                    $isPromoted[$game->getId()] = true;
                } else {
                    $isPromoted[$game->getId()] = false;
                }
            } else {
                $isPromoted[$game->getId()] = false;
            }
        }

        $games = $gameRepository->sortBy($sort, $order, $id);

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($data['promotions'] == '') {
                $promo = 0;
            } else {
                $promo = 1;
            }
            
            return $this->redirectToRoute('category_search', [
                'id' => $id,
                'input' => $data['input'],
                'rangePrice' => $data['range'],
                'promotions' => $promo
            ]);
        }
        
        return $this->render('categories/index.html.twig', [
            'controller_name' => 'CategoriesController',
            'games' => $games,
            'categories' => $categories,
            'title' => $title,
            'avgRatings' => $avgRatings,
            'starsNbr' => $starsNbr,
            'isPromoted' => $isPromoted,
            'form' => $form->createView()
        ]);

    }

    #[Route('/categories/{id}/search/{input}/{rangePrice}/{promotions}', name: 'category_search')]
    public function search(int $id, string $input, int $rangePrice, int $promotions, GameRepository $gameRepository, CategoryRepository $categoryRepository, Request $request, GameReviewService $gameReviewService): Response
    {

        $games = $gameRepository->findByName($input, $rangePrice, $id);
        $categories = $categoryRepository->findAll();
        $selectedCategory = $categoryRepository->find($id)->getName();
        $title = "Custom filters on $selectedCategory";

        $isPromoted = [];

        foreach ($games as $game) {
            $avgRatings[$game->getId()] = $gameReviewService->getAvgReview($game);
            $starsNbr[$game->getId()] = round($avgRatings[$game->getId()][0], 1);
            $avgRatings[$game->getId()][0] = number_format($avgRatings[$game->getId()][0], 1, '.', '');

            if ($game->getPromotion() !== null) {
                $now = new \DateTime('now');
                if ($game->getPromotionStart() <= $now && $game->getPromotionEnd() >= $now) {
                    $isPromoted[$game->getId()] = true;
                } else {
                    $isPromoted[$game->getId()] = false;
                }
            } else {
                $isPromoted[$game->getId()] = false;
            }

            if ($promotions != 0) {
                if (!$isPromoted[$game->getId()]) {
                    unset($games[array_search($game, $games)]);
                }
            }
        }

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($data['promotions'] == '') {
                $promo = 0;
            } else {
                $promo = 1;
            }
            
            return $this->redirectToRoute('category_search', [
                'id' => $id,
                'input' => $data['input'],
                'rangePrice' => $data['range'],
                'promotions' => $promo
            ]);
        }

        return $this->render('categories/index.html.twig', [
            'controller_name' => 'CategoriesController',
            'games' => $games,
            'categories' => $categories,
            'title' => $title,
            'avgRatings' => $avgRatings,
            'starsNbr' => $starsNbr,
            'isPromoted' => $isPromoted,
            'form' => $form->createView()
        ]);
    }
}
