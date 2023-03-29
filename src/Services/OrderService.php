<?php

namespace App\Services;

use App\Repository\OrderLineRepository;
use App\Repository\OrderRepository;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use App\Entity\Order;
use App\Entity\OrderLine;
use App\Services\CartService;
use App\Entity\User;
use App\Entity\Game;

class OrderService
{
    protected OrderRepository $orderRepository;
    protected OrderLineRepository $orderLineRepository;
    protected GameRepository $gameRepository;
    protected UserRepository $userRepository;

    public function __construct(OrderRepository $orderRepository, OrderLineRepository $orderLineRepository, GameRepository $gameRepository, UserRepository $userRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->orderLineRepository = $orderLineRepository;
        $this->gameRepository = $gameRepository;
        $this->userRepository = $userRepository;
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
            $order->addOrderLine($orderLine);
            $this->orderLineRepository->save($orderLine, true);
        }



        return $order;
    }

    public function payOrder(Order $order): Order
    {
        $order->setStatus('paid at : ' . date('Y-m-d H:i:s'));
        $this->orderRepository->save($order, true);

        $orderLines = $order->getOrderLines();
        foreach ($orderLines as $orderLine) {
            $game = $orderLine->getGame();

            $user = $order->getUser();

            $game->addOwner($user);
            $game->addSold();
            $this->gameRepository->save($game, true);

            $user->addGamesOwned($game);
            $this->userRepository->save($user, true);
        }

        return $order;
    }

    public function sendOrder(Order $order): Order
    {
        $order->setStatus('sent at : ' . date('Y-m-d H:i:s'));
        $this->orderRepository->save($order, true);
        return $order;
    }
}
