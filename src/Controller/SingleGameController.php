<?php

namespace App\Controller;

use App\Repository\GameRepository;
use App\Services\GameReviewService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\PostReviewType;
use App\Entity\Review;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;

class SingleGameController extends AbstractController
{
    #[Route('/game/{id}', name: 'app_single_game', methods: ['GET'])]
    public function index(int $id, GameRepository $gameRepository, GameReviewService $gameReviewService): Response
    {
        $user = $this->getUser();

        $game = $gameRepository->find($id);

        $gameCategories = $game->getCategory()->map(function ($category) {
            return $category->getId();
        })->toArray();

        // using findByCategories find related games
        $gamesRelated = $gameRepository->findByCategories($gameCategories);
        // remove current game from related games
        $gamesRelated = array_filter($gamesRelated, function ($gameRelated) use ($game) {
            return $gameRelated->getId() !== $game->getId();
        });

        //  dump($gamesRelated);

        $finalGamesRelated = [];

        if (count($gamesRelated) > 3) {
            for ($i = 0; $i <= 3; $i++) {
                $randomGame = array_rand($gamesRelated, 1);
                $finalGamesRelated[] = $gamesRelated[$randomGame];
                unset($gamesRelated[$randomGame]);
            }
        } else {
            $finalGamesRelated = $gamesRelated;
        }



        //  dump($finalGamesRelated);

        $reviews = $game->getReviews();

        $avgReview = $gameReviewService->getAvgReview($game);

        if ($user) {

            $reviewed = $gameReviewService->hasUserReviewedGame($user, $game);
            // dump($reviewed);
            if (!$reviewed) {

                // create form using PostReviewType
                $form = $this->createForm(PostReviewType::class);

                return $this->render('single_game/index.html.twig', [
                    'controller_name' => 'SingleGameController',
                    'form' => $form,
                    'game' => $game,
                    'avgReview' => $avgReview,
                    'reviews' => $reviews,
                    'gamesRelated' => $finalGamesRelated
                ]);
            }
        }



        return $this->render('single_game/index.html.twig', [
            'controller_name' => 'SingleGameController',
            'game' => $game,
            'avgReview' => $avgReview,
            'reviews' => $reviews,
            'gamesRelated' => $gamesRelated
        ]);
    }

    #[Route('/game/{id}', name: 'app_single_game_post', methods: ['POST'])]
    public function postReview(int $id, GameRepository $gameRepository, GameReviewService $gameReviewService, Request $request, ReviewRepository $reviewRepository): Response
    {
        $user = $this->getUser();

        $game = $gameRepository->find($id);

        $avgReview = $gameReviewService->getAvgReview($game);

        if ($user) {

            $reviewed = $gameReviewService->hasUserReviewedGame($user, $game);

            if (!$reviewed) {

                // create form using PostReviewType
                $form = $this->createForm(PostReviewType::class);

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $review = $form->getData();

                    $review->setAuthor($user);
                    $review->setGames($game);

                    $review->setCreationDate(new \DateTime());

                    $reviewRepository->save($review, true);

                    return $this->redirectToRoute('app_single_game', ['id' => $id]);
                }
            }
        }
    }
}
