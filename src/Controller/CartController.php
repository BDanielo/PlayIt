<?php

namespace App\Controller;

use App\Services\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart', methods: ['GET'])]
    public function index(CartService $cartService): Response
    {
        $cart = $cartService->getCart();
        // dump($cart);
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'cart' => $cart
        ]);
    }

    #[Route('/cart/add/{id}/ajax', name: 'app_cart_add_ajax', methods: ['GET'])]
    public function add_ajax(int $id, CartService $cartService): Response
    {
        $cartService->addToCart($id);
        return $this->sendResponse(['status' => 'ok']);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add', methods: ['GET'])]
    public function add(int $id, CartService $cartService): Response
    {
        $cartService->addToCart($id);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/decrease/{id}/ajax', name: 'app_cart_decrease_ajax', methods: ['GET'])]
    public function decrease_ajax(int $id, CartService $cartService): Response
    {
        $cartService->decreaseInCart($id);
        return $this->sendResponse(['status' => 'ok']);
    }

    #[Route('/cart/decrease/{id}', name: 'app_cart_decrease', methods: ['GET'])]
    public function decrease(int $id, CartService $cartService): Response
    {
        $cartService->decreaseInCart($id);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{id}/ajax', name: 'app_cart_remove_ajax', methods: ['GET'])]
    public function remove_ajax(int $id, CartService $cartService): Response
    {
        $cartService->removeFromCart($id);
        return $this->sendResponse(['status' => 'ok']);
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove', methods: ['GET'])]
    public function remove(int $id, CartService $cartService): Response
    {
        $cartService->removeFromCart($id);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/clear', name: 'app_cart_clear', methods: ['GET'])]
    public function clear(CartService $cartService): Response
    {
        $cartService->clearCart();
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/clear/ajax', name: 'app_cart_clear_ajax', methods: ['GET'])]
    public function clear_ajax(CartService $cartService): Response
    {
        $cartService->clearCart();
        return $this->sendResponse(['status' => 'ok']);
    }

    public function sendResponse($data, $status = 200)
    {
        $response = new Response();
        $response->setContent(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode($status);
        return $response;
    }
}
