<?php

namespace App\Controller;

use App\Repository\GameRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyGamesController extends AbstractController
{
    #[Route('/user/my-games', name: 'app_my_games')]
    public function index(UserRepository $userRepository): Response
    {
        if ($this->getUser()) {
            //** @var User $user */
            $user = $this->getUser();
            $user->setLastSigninDateTime(new \DateTime('now'));
            $userRepository->save($user, true);
        }

        // get the current user
        $user = $this->getUser();

        // get all games owned by the current user
        $games = $user->getGamesOwned();

        return $this->render('my_games/index.html.twig', [
            'controller_name' => 'MyGamesController',
            'games' => $games,
        ]);
    }
}
