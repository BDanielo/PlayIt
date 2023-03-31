<?php

namespace App\Services;


use App\Entity\Game;
use App\Entity\User;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class CartService
{

    protected Session $session;
    protected GameRepository $gameRepository;

    protected UserRepository $userRepository;
    public function __construct(RequestStack $requestStack, GameRepository $repository, UserRepository $userRepository)
    {
        $this->session = $requestStack->getSession();
        $this->gameRepository = $repository;
        $this->userRepository = $userRepository;
    }
    public function addToCart(int $id, int $userId): array
    {
        $result = ['', ''];
        if (!$this->checkProductValidity($id)) {
            throw new \Exception("Product not found");
        }
        $user = $this->userRepository->find($userId);

        if ($user) {
            if ($this->checkAlreadyOwned($this->gameRepository->find($id), $user)) {
                $result = ['error', 'already owned'];
                return $result;
            }
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
        $this->session->set('coupon', null);
    }

    // get coupon
    public function getCoupon()
    {
        $coupon = $this->session->get('coupon');
        return $coupon;
    }

    // get coupon price
    public function getCouponPrice()
    {
        $coupon = $this->session->get('coupon');
        if ($coupon == null) {
            return 0;
        } else {
            return $coupon->getPrice();
        }
    }

    // set coupon
    public function setCoupon($coupon)
    {
        $this->session->set('coupon', $coupon);

        $coupon = $this->getCoupon();

        return $coupon;
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

    public function getCart(User $user = null)
    {
        $cart = $this->session->get('cart');
        $games = [];
        $total = 0;
        if ($cart == null || count($cart) == 0) {
            return null;
        } else {
            foreach ($cart as $id => $quantity) {
                $game = $this->gameRepository->find($id);
                $owned = false;
                if ($user != null) {
                    if ($this->checkAlreadyOwned($game, $user)) {
                        $owned = true;
                    }
                }
                $games[] = [
                    'gameEntity' => $game,
                    'quantity' => $quantity,
                    'owned' => $owned
                ];
                // check if game is on promotion, with getPromotionPrice, and if promotionStart is before current date and promotionEnd is after current date
                if ($game->hasPromotion() == false) {
                    $total += $game->getPrice() * $quantity;
                } else {
                    $total += $game->getPromotionPrice() * $quantity;
                }
            }

            $coupon = $this->getCoupon();
            if ($coupon == null) {
                $coupon = 0;
            } else {
                $coupon = $coupon->getPercent();
            }
            $coupon = $total * ($coupon / 100);
            $total -= $coupon;

            return [
                'games' => $games,
                'total' => $total,
                'coupon' => $coupon
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
                if ($game->getPromotionPrice() == null && $game->getPromotionStart() == null && $game->getPromotionEnd() == null) {
                    $total += $game->getPrice() * $quantity;
                } else {
                    $total += $game->getPromotionPrice() * $quantity;
                }
            }

            $coupon = $this->getCoupon();
            if ($coupon == null) {
                $coupon = 0;
            } else {
                $coupon = $coupon->getPercent();
            }
            $coupon = $total * ($coupon / 100);
            $total -= $coupon;

            return $total;
        }
    }

    public function checkAlreadyOwned(Game $game, User $user): bool
    {
        return $games = $user->isGameOwned($game);
    }
}
