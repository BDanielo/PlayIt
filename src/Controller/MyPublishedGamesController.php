<?php

namespace App\Controller;

use App\DTO\CreatePromotionDTO;
use App\Entity\Game;
use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\AddGameType;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Form\CreatePromotionType;

class MyPublishedGamesController extends AbstractController
{
    #[Route('/dev/published-games', name: 'app_published_games')]
    public function index(UserRepository $userRepository, GameRepository $gameRepository): Response
    {

        //get current user
        //** @var User $user */
        $user = $this->getUser();

        if ($user->getRoles()[0] == 'ROLE_ADMIN') {
            // get all published games
            $games = $gameRepository->findAll();

            return $this->render('my_published_games/index.html.twig', [
                'controller_name' => 'MyPublishedGamesController',
                'games' => $games,
            ]);
        }

        // get all published games of the current user
        $games = $userRepository->find($user->getId())->getGamesPublished();

        return $this->render('my_published_games/index.html.twig', [
            'controller_name' => 'MyPublishedGamesController',
            'games' => $games,
        ]);
    }

    #[Route('/dev/published-games/add', name: 'app_published_games_add', methods: ['GET'])]
    public function add(CategoryRepository $categoryRepository): Response
    {
        // get all categories
        $categories = $categoryRepository->findAll();

        $form = $this->createForm(AddGameType::class);

        // add category to the form and show their names 
        // $form->add('category', ChoiceType::class, [
        //     'choices' => $categories,
        //     'choice_label' => function ($category) {
        //         return $category->getName();
        //     },
        // ]);

        return $this->render('my_published_games/add.html.twig', [
            'controller_name' => 'MyPublishedGamesController',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dev/published-games/add', name: 'app_published_games_add_post', methods: ['POST'])]
    public function addPost(Request $request, GameRepository $gameRepository): Response
    {
        $form = $this->createForm(AddGameType::class);

        $form->handleRequest($request);

        // from the form, get the data and create a new game

        // get current user
        //** @var User $user */
        $user = $this->getUser();

        // create a new game
        $game = new Game();

        // set the game's name
        $game->setName($form->get('name')->getData());

        // set the game's description
        $game->setDescription($form->get('description')->getData());

        // set the game's price
        $game->setPrice($form->get('price')->getData());

        // set the game's version
        $game->setVersion($form->get('version')->getData());

        // set the game's picture
        $game->setPicture($form->get('picture')->getData());

        // set the game's file
        $game->setFile($form->get('file')->getData());

        // set the game's author to the current user
        $game->addAuthor($user);

        // set the game's creation date to now
        $game->setCreationDate(new \DateTime());

        // for each category, add it to the game's category
        foreach ($form->get('category')->getData() as $category) {
            $game->addCategory($category);
        }

        // set the game's author
        $game->addAuthor($user);

        // using the game repository, save the game
        $gameRepository->save($game, true);

        // if the game is saved, show a success message and return to the published games page
        if ($game->getId()) {
            $this->addFlash('success', 'Game added successfully');
            return $this->redirectToRoute('app_published_games');
        } else {
            return $this->render('my_published_games/add.html.twig', [
                'controller_name' => 'MyPublishedGamesController',
                'form' => $form->createView(),
                'message' => 'yes sir',
            ]);
        }
    }


    #[Route('/dev/published-games/edit/{id}', name: 'app_published_games_edit', methods: ['GET', 'POST'])]

    public function edit(Game $game, GameRepository $gameRepository, CategoryRepository $categoryRepository, Request $request): Response
    {
        // get all categories
        //$categories = $categoryRepository->findAll();

        $form = $this->createForm(AddGameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $game->setUpdateDate(new \DateTime('now'));
            $gameRepository->save($game, true);

            return $this->redirectToRoute('app_published_games', [], Response::HTTP_SEE_OTHER);
        }

        // add category to the form and show their names 
        // $form->add('category', ChoiceType::class, [
        //     'choices' => $categories,
        //     'choice_label' => function ($category) {
        //         return $category->getName();
        //     },
        // ]);

        return $this->render('my_published_games/edit.html.twig', [
            'controller_name' => 'MyPublishedGamesController',
            'form' => $form->createView(),
            'game' => $game,
        ]);
    }

    #[Route('/dev/published-games/delete/{id}', name: 'app_published_games_delete', methods: ['GET', 'POST'])]

    public function delete(Game $game, GameRepository $gameRepository): Response
    {
        // using the game repository, delete the game
        $gameRepository->delete($game, true);

        // if the game is deleted, show a success message and return to the published games page
        if (!$game->getId()) {
            $this->addFlash('success', 'Game deleted successfully');
            return $this->redirectToRoute('app_published_games');
        } else {
            return $this->render('my_published_games/edit.html.twig', [
                'controller_name' => 'MyPublishedGamesController',
                'game' => $game,
            ]);
        }
    }

    #[Route('/dev/published-games/promotion/{id}', name: 'app_published_games_promotion', methods: ['GET'])]

    public function promotion(int $id, GameRepository $gameRepository): Response
    {
        // check if the game exists
        $game = $gameRepository->find($id);

        if (!$game) {
            $this->addFlash('error', 'Game not found');
            return $this->redirectToRoute('app_published_games');
        }

        $dto = new CreatePromotionDTO();

        $form = $this->createForm(CreatePromotionType::class, $dto);

        return $this->render('my_published_games/promotion.html.twig', [
            'controller_name' => 'MyPublishedGamesController',
            'form' => $form
        ]);
    }

    #[Route('/dev/published-games/promotion{id}', name: 'app_published_games_promotion_post', methods: ['POST'])]
    public function promotionPost(int $id, Request $request, GameRepository $gameRepository): Response
    {
        // check if the game exists
        $game = $gameRepository->find($id);

        if (!$game) {
            $this->addFlash('error', 'Game not found');
            return $this->redirectToRoute('app_published_games');
        }

        $dto = new CreatePromotionDTO();

        $form = $this->createForm(CreatePromotionType::class, $dto);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $percent = $dto->percent;
            $date_start = $dto->date_start;
            $date_end = $dto->date_end;
        }

        return $this->render('my_published_games/promotion.html.twig', [
            'controller_name' => 'MyPublishedGamesController',
            'form' => $form
        ]);
    }
}
