<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyOrdersController extends AbstractController
{
    #[Route('/my/orders', name: 'app_my_orders')]
    public function index(): Response
    {
        return $this->render('my_orders/index.html.twig', [
            'controller_name' => 'MyOrdersController',
        ]);
    }
}
