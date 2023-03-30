<?php

namespace App\Controller;

use App\Repository\OrderLineRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyOrdersController extends AbstractController
{
    #[Route('/user/my-orders', name: 'app_my_orders')]
    public function index(OrderRepository $orderRepository, OrderLineRepository $orderLineRepository, UserRepository $userRepository): Response
    {
        if ($this->getUser()) {
            //** @var User $user */
            $user = $this->getUser();
            $user->setLastSigninDateTime(new \DateTime('now'));
            $userRepository->save($user, true);
        }

        // get all orders from the current user
        $user = $this->getUser();

        $orders = $orderRepository->findBy(['user' => $user]);

        // for each order get the order lines and add them to an array
        $ordersWithLines = [];
        foreach ($orders as $order) {
            $orderLines = $orderLineRepository->findBy(['orderR' => $order]);
            $ordersWithLines[] = [
                'order' => $order,
                'orderLines' => $orderLines
            ];
        }
        // dump($ordersWithLines);

        return $this->render('my_orders/index.html.twig', [
            'controller_name' => 'MyOrdersController',
            'ordersWithLines' => $ordersWithLines
        ]);
    }
}
