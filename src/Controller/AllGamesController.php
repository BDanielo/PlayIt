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

class AllGamesController extends AbstractController
{
    #[Route('/all-games', name: 'app_all_games')]
    public function index(GameRepository $gamesRepository, CategoryRepository $categoryRepository, Request $request, GameReviewService $gameReviewService): Response
    {
        $title = 'All Games';
        $games = $gamesRepository->findAll();
        $categories = $categoryRepository->findAll();

        $starsNbr = [];
        $avgRatings = [];
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

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($data['promotions'] == '') {
                $promo = 0;
            } else {
                $promo = 1;
            }

            return $this->redirectToRoute('app_search', [
                'input' => $data['input'],
                'rangePrice' => $data['range'],
                'promotions' => $promo
            ]);
        }

        return $this->render('all_games/index.html.twig', [
            'controller_name' => 'AllGamesController',
            'games' => $games,
            'categories' => $categories,
            'title' => $title,
            'avgRatings' => $avgRatings,
            'starsNbr' => $starsNbr,
            'isPromoted' => $isPromoted,
            'form' => $form->createView()
        ]);
    }

    #[Route('/all-games/orderby/{sort}/{order}', name: 'app_sorted_games')]

    public function orderBy(string $sort, string $order, GameRepository $gameRepository, CategoryRepository $categoryRepository, Request $request, GameReviewService $gameReviewService): Response
    {
        $title = 'All Games';
        $categories = $categoryRepository->findAll();

        $games = $gameRepository->sortBy($sort, $order, null);

        if ($sort == 'price') {
            if ($order == 'ASC') {
                usort($games, function ($a, $b) {
                    if ($a->getPromotion() !== null) {
                        $aPrice = $a->getPromotionPrice();
                    } else {
                        $aPrice = $a->getPrice();
                    }

                    if ($b->getPromotion() !== null) {
                        $bPrice = $b->getPromotionPrice();
                    } else {
                        $bPrice = $b->getPrice();
                    }

                    return $aPrice <=> $bPrice;
                });
            } else {
                usort($games, function ($a, $b) {
                    if ($a->getPromotion() !== null) {
                        $aPrice = $a->getPromotionPrice();
                    } else {
                        $aPrice = $a->getPrice();
                    }

                    if ($b->getPromotion() !== null) {
                        $bPrice = $b->getPromotionPrice();
                    } else {
                        $bPrice = $b->getPrice();
                    }

                    return $bPrice <=> $aPrice;
                });
            }
        }

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

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($data['promotions'] == '') {
                $promo = 0;
            } else {
                $promo = 1;
            }

            return $this->redirectToRoute('app_search', [
                'input' => $data['input'],
                'rangePrice' => $data['range'],
                'promotions' => $promo
            ]);
        }

        return $this->render('all_games/index.html.twig', [
            'controller_name' => 'AllGamesController',
            'games' => $games,
            'categories' => $categories,
            'title' => $title,
            'avgRatings' => $avgRatings,
            'starsNbr' => $starsNbr,
            'isPromoted' => $isPromoted,
            'form' => $form->createView()
        ]);
    }

    #[Route('/all-games/search/{input}/{rangePrice}/{promotions}', name: 'app_search')]

    public function search(string $input, int $rangePrice, int $promotions, GameRepository $gameRepository, CategoryRepository $categoryRepository, Request $request, GameReviewService $gameReviewService): Response
    {
        $title = 'Personal filters';
        $games = $gameRepository->findByName($input, $rangePrice, null);
        $categories = $categoryRepository->findAll();

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

            return $this->redirectToRoute('app_search', [
                'input' => $data['input'],
                'rangePrice' => $data['range'],
                'promotions' => $promo
            ]);
        }

        return $this->render('all_games/index.html.twig', [
            'controller_name' => 'AllGamesController',
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
