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
            $games = $gameRepository->findAllCrud();

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
        // get user
        $user = $this->getUser();

        if (!$this->isGranted('ROLE_ADMIN') && $this->isGranted('ROLE_DEV') && !$game->isAuthor($user)) {
            $this->addFlash('error', 'You are not allowed to edit this game');
            return $this->redirectToRoute('app_published_games');
        }


        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_DEV')) {
            $this->addFlash('error', 'You are not allowed to edit this game');
            return $this->redirectToRoute('app_home');
        }


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
        // check if the game exists
        if (!$game) {
            $this->addFlash('error', 'Game not found');
            return $this->redirectToRoute('app_published_games');
        }

        if ($this->isGranted('ROLE_DEV') && !$this->isGranted('ROLE_ADMIN')) {
            if (!$game->getAuthors()->contains($this->getUser())) {
                $this->addFlash('error', 'You are not allowed to delete this game');
                return $this->redirectToRoute('app_published_games');
            }

            // set game status to 3
            $game->setStatus(3);
            $gameRepository->save($game, true);

            $this->addFlash('success', 'Game deleted successfully');
            return $this->redirectToRoute('app_published_games');
        }

        // if user is not admin
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'You are not allowed to delete games');
            return $this->redirectToRoute('app_published_games');
        }

        $gameRepository->remove($game, true);

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

    #[Route('/dev/published-games/approve/{id}', name: 'app_published_games_approve', methods: ['GET'])]
    public function approve(int $id, GameRepository $gameRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'You are not allowed to approve games');
            return $this->redirectToRoute('app_published_games');
        }

        $game = $gameRepository->find($id);

        if (!$game) {
            $this->addFlash('error', 'Game not found');
            return $this->redirectToRoute('app_published_games');
        }

        $game->setStatus(1);
        $gameRepository->save($game, true);

        $this->addFlash('success', 'Game approved successfully');
        return $this->redirectToRoute('app_published_games');
    }

    #[Route('/dev/published-games/reject/{id}', name: 'app_published_games_reject', methods: ['GET'])]
    public function reject(int $id, GameRepository $gameRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'You are not allowed to reject games');
            return $this->redirectToRoute('app_published_games');
        }

        $game = $gameRepository->find($id);

        if (!$game) {
            $this->addFlash('error', 'Game not found');
            return $this->redirectToRoute('app_published_games');
        }

        $game->setStatus(2);
        $gameRepository->save($game, true);

        $this->addFlash('success', 'Game rejected successfully');
        return $this->redirectToRoute('app_published_games');
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

        // fill dto with data from game promotion
        // set promotion 
        $dto->promotion = $game->getPromotion();
        // promotionStart
        $dto->promotionStart = $game->getPromotionStart();
        // promotionEnd
        $dto->promotionEnd = $game->getPromotionEnd();

        $form = $this->createForm(CreatePromotionType::class, $dto);


        return $this->render('my_published_games/promotion.html.twig', [
            'controller_name' => 'MyPublishedGamesController',
            'form' => $form->createView(),
            'game' => $game,
        ]);
    }

    #[Route('/dev/published-games/promotion/{id}', name: 'app_published_games_promotion_post', methods: ['POST'])]
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
            $percent = $dto->promotion;
            $date_start = $dto->promotionStart;
            $date_end = $dto->promotionEnd;


            if ($date_start > $date_end) {
                $this->addFlash('error', 'The start date must be before the end date');
                return $this->render('my_published_games/promotion.html.twig', [
                    'controller_name' => 'MyPublishedGamesController',
                    'form' => $form
                ]);
            }

            $game->setPromotion($percent);
            $game->setPromotionStart($date_start);
            $game->setPromotionEnd($date_end);

            $gameRepository->save($game, true);

            return $this->redirectToRoute('app_published_games', [], Response::HTTP_SEE_OTHER);
        }


        return $this->render('my_published_games/promotion.html.twig', [
            'controller_name' => 'MyPublishedGamesController',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dev/published-games/promotion/{id}', name: 'app_published_games_promotion_delete', methods: ['POST'])]
    public function promotionDelete(int $id, GameRepository $gameRepository): Response
    {
        // check if the game exists
        $game = $gameRepository->find($id);

        if (!$game) {
            $this->addFlash('error', 'Game not found');
            return $this->redirectToRoute('app_published_games');
        }

        $game->setPromotion(null);
        $game->setPromotionStart(null);
        $game->setPromotionEnd(null);

        $gameRepository->save($game, true);

        return $this->redirectToRoute('app_published_games', [], Response::HTTP_SEE_OTHER);
    }
}
