<?php

namespace App\Services;


use App\Repository\GameRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class CartService
{

    protected Session $session;
    protected GameRepository $gameRepository;
    public function __construct(RequestStack $requestStack, GameRepository $repository)
    {
        $this->session = $requestStack->getSession();
        $this->gameRepository = $repository;
    }
    public function addToCart(int $id): array
    {
        $result = ['', ''];
        if (!$this->checkProductValidity($id)) {
            throw new \Exception("Product not found");
        }

        $cart = $this->initCart();
        try {
            $quantity = $cart[$id];
        } catch (\Throwable $th) {
            $quantity = 0;
        }

        // $cart[$id] = $quantity + 1;
        if ($quantity > 0) {
            $cart[$id] = 1;
            $result = ['error', 'already in cart'];
        } else {
            $cart[$id] = 1;
            $result = ['success', 'added to cart'];
        }
        $this->session->set('cart', $cart);
        return $result;
    }


    public function decreaseInCart(int $id)
    {
        $cart = $this->session->get('cart');
        $quantity = $cart[$id];

        if ($quantity > 1) {
            $quantity -= 1;
            $cart[$id] = $quantity;
        } else {
            unset($cart[$id]);
        }

        $this->session->set('cart', $cart);
    }

    public function removeFromCart(int $id)
    {
        $cart = $this->session->get('cart');
        unset($cart[$id]);
        $this->session->set('cart', $cart);
    }

    public function clearCart()
    {
        $this->session->set('cart', []);
    }

    // get coupon
    public function getCoupon()
    {
        $coupon = $this->session->get('coupon');
        return $coupon;
    }

    // set coupon
    public function setCoupon($coupon)
    {
        $this->session->set('coupon', $coupon);
    }

    public function initCart()
    {
        $cart = $this->session->get('cart');

        if ($cart == null) {
            $this->session->set('cart', []);
            $cart = [];
        }

        return $cart;
    }

    public function getCart()
    {
        $cart = $this->session->get('cart');
        $games = [];
        $total = 0;
        if ($cart == null || count($cart) == 0) {
            return null;
        } else {
            foreach ($cart as $id => $quantity) {
                $game = $this->gameRepository->find($id);
                $games[] = [
                    'gameEntity' => $game,
                    'quantity' => $quantity
                ];
                $total += $game->getPrice() * $quantity;
            }
            return [
                'games' => $games,
                'total' => $total
            ];
        }
    }

    public function checkProductValidity(int $id)
    {
        $game = $this->gameRepository->find($id);
        if ($game == null) {
            return false;
        } else {
            return true;
        }
    }

    public function getCartPrice()
    {
        $cart = $this->session->get('cart');
        $total = 0;
        if ($cart == null || count($cart) == 0) {
            return null;
        } else {
            foreach ($cart as $id => $quantity) {
                $game = $this->gameRepository->find($id);
                $total += $game->getPrice() * $quantity;
            }
            return $total;
        }
    }
}
