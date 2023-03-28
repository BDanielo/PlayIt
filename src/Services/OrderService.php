<?php

namespace App\Services;

use App\Repository\OrderLineRepository;
use App\Repository\OrderRepository;
use App\Entity\Order;
use App\Entity\OrderLine;
use App\Services\CartService;
use App\Entity\User;
use App\Entity\Game;

class OrderService
{
    protected OrderRepository $orderRepository;
    protected OrderLineRepository $orderLineRepository;

    public function __construct(OrderRepository $orderRepository, OrderLineRepository $orderLineRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->orderLineRepository = $orderLineRepository;
    }

    public function createOrder(CartService $cartService, User $user, $total): Order
    {
        $order = new Order();
        $order = $order->setStatus('pending');
        $order = $order->setUser($user);
        $order = $order->setTotal($total);
        $this->orderRepository->save($order, true);

        $cart = $cartService->getCart();
        $games = $cart['games'];
        foreach ($games as $game) {
            $gameEntity = new Game;
            $gameEntity = $game['gameEntity'];

            $orderLine = new OrderLine();
            $orderLine->setGame($gameEntity);
            $orderLine->setQuantity($game['quantity']);
            $orderLine->setPrice($gameEntity->getPrice() * $game['quantity']);
            $orderLine->setOrderR($order);
            $this->orderLineRepository->save($orderLine, true);
        }



        return $order;
    }

    public function payOrder(Order $order): Order
    {
        $order->setStatus('paid at : ' . date('Y-m-d H:i:s'));
        $this->orderRepository->save($order, true);
        return $order;
    }

    public function sendOrder(Order $order): Order
    {
        $order->setStatus('sent at : ' . date('Y-m-d H:i:s'));
        $this->orderRepository->save($order, true);
        return $order;
    }
}
