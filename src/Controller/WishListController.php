<?php

namespace App\Controller;

use App\Repository\GameRepository;
use App\Repository\WishListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WishListController extends AbstractController
{
    #[Route('/wish-list', name: 'app_wish_list')]
    public function index(WishListRepository $wishListRepository): Response
    {
        //** @var User $user */
        $user = $this->getUser();
        $id = $user->getId();

        $wishList = $wishListRepository->findWishListByUser($id);

        $games = $wishList->getGames();

        return $this->render('wish_list/index.html.twig', [
            'controller_name' => 'WishListController',
            'games' => $games,
        ]);
    }

    #[Route('/wish-list/add/{gameId}', name: 'app_wish_list_add')]

    public function add(int $gameId, WishListRepository $wishListRepository, GameRepository $gameRepository): Response
    {
        //** @var User $user */
        $user = $this->getUser();
        $id = $user->getId();

        $wishList = $wishListRepository->findWishListByUser($id);

        $game = $gameRepository->find($gameId);

        $wishList->addGame($game);

        $wishListRepository->save($wishList, true);

        return $this->redirectToRoute('app_wish_list', ['id' => $id]);
    }

    #[Route('/wish-list/remove/{gameId}', name: 'app_wish_list_remove')]

    public function remove(int $gameId, WishListRepository $wishListRepository, GameRepository $gameRepository): Response
    {
        //** @var User $user */
        $user = $this->getUser();
        $id = $user->getId();

        $wishList = $wishListRepository->findWishListByUser($id);

        $game = $gameRepository->find($gameId);

        $wishList->removeGame($game);

        $wishListRepository->save($wishList, true);

        return $this->redirectToRoute('app_wish_list', ['id' => $id]);
    }
}
