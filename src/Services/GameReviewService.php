<?php

namespace App\Services;

use App\Repository\GameRepository;
use App\Entity\Game;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Entity\Review;

class GameReviewService
{
    protected GameRepository $gameRepository;

    protected UserRepository $userRepository;


    public function __construct(GameRepository $gameRepository, UserRepository $userRepository)
    {
        $this->gameRepository = $gameRepository;
        $this->userRepository = $userRepository;
    }

    public function getAvgReview(Game $game): array
    {
        $reviews = $game->getReviews();
        $sum = 0;
        if (count($reviews) == 0) {
            return [0, 0];
        }

        foreach ($reviews as $review) {
            $sum += $review->getRate();
        }
        $avg = $sum / count($reviews);
        return [$avg, count($reviews)];
    }

    public function hasUserReviewedGame(User $user, Game $game): bool
    {
        // get user reviews
        $userReviews = $user->getReviews();

        // check if the user owns the game
        $userGames = $user->getGamesOwned();

        $owned = false;

        foreach ($userGames as $userGame) {
            if ($userGame == $game) {
                $owned = true;
            }
        }

        // if (!$owned) {
        //     return true;
        // }

        // for each review check if the game is the same
        foreach ($userReviews as $review) {
            if ($review->getGames() == $game) {
                return true;
            }
        }
        return false;
    }
}
