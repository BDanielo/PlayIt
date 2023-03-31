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

        // get the wishlist of the user by its id
        $wishList = $wishListRepository->findWishListByUser($id);

        // get all game
        $games = $wishList->getGames();

        // render the wishlist page
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

        // get wishlist by user id
        $wishList = $wishListRepository->findWishListByUser($id);

        // check if wishlist is valid, if not throw exception
        if (!$wishList) {
            throw new \Exception('Wishlist not found');
        }

        // get game by game id
        $game = $gameRepository->find($gameId);

        // check if the game if valid, if not throw exception
        if (!$game) {
            throw new \Exception('Game not found');
        }

        // add the game to the wishlist
        $wishList->addGame($game);

        // save the wishlist in the db
        $wishListRepository->save($wishList, true);

        // redirect to the wishlist page
        return $this->redirectToRoute('app_wish_list', ['id' => $id]);
    }

    #[Route('/wish-list/remove/{gameId}', name: 'app_wish_list_remove')]

    public function remove(int $gameId, WishListRepository $wishListRepository, GameRepository $gameRepository): Response
    {
        //** @var User $user */
        $user = $this->getUser();
        $id = $user->getId();

        // get wishlist by user id
        $wishList = $wishListRepository->findWishListByUser($id);

        // check if wishlist is valid, if not throw exception
        if (!$wishList) {
            throw new \Exception('Wishlist not found');
        }

        // get game by game id
        $game = $gameRepository->find($gameId);

        // check if the game if valid, if not throw exception
        if (!$game) {
            throw new \Exception('Game not found');
        }

        // remove the game from the wishlist
        $wishList->removeGame($game);

        // save the wishlist in the db
        $wishListRepository->save($wishList, true);

        // redirect to the wishlist page
        return $this->redirectToRoute('app_wish_list', ['id' => $id]);
    }
}
