<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListCategoriesController extends AbstractController
{
    #[Route('/list/categories', name: 'app_list_categories')]
    public function index(CategoryRepository $categoryRepository): Response
    {

        $categories = $categoryRepository->findAll();

        return $this->render('list_categories/index.html.twig', [
            'controller_name' => 'ListCategoriesController',
            'categories' => $categories,
        ]);
    }
}
