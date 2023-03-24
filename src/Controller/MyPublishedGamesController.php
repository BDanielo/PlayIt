<?php

namespace App\Controller;

use App\Entity\Games;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\AddGameType;
use App\Repository\CategoryRepository;
use App\Repository\GamesRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class MyPublishedGamesController extends AbstractController
{
    #[Route('/dev/published-games', name: 'app_published_games')]
    public function index(): Response
    {
        return $this->render('my_published_games/index.html.twig', [
            'controller_name' => 'MyPublishedGamesController',
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
            'message' => 'base',
        ]);
    }

    #[Route('/dev/published-games/add', name: 'app_published_games_add_post', methods: ['POST'])]
    public function addPost(Request $request, GamesRepository $gamesRepository): Response
    {
        $form = $this->createForm(AddGameType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $game = new Games();
            $game->setName($data['name']);
            $game->setPrice($data['price']);
            $game->setDescription($data['description']);
            //!TODO add category
            $game->addCategory($data['category']);
            $game->setVersion($data['version']);
            $game->setPicture($data['picture']);

            $gamesRepository->save($game, true);



            return $this->redirectToRoute('task_success');
        }

        return $this->render('my_published_games/add.html.twig', [
            'controller_name' => 'MyPublishedGamesController',
            'form' => $form->createView(),
            'message' => 'Something went wrong',
        ]);
    }
}
