<?php

namespace App\Controller;

use App\DTO\CardVerificationDTO;
use App\Form\AddCouponType;
use App\Services\BadgeService;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PointsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CardVerificationType;
use App\Repository\CouponRepository;
use App\Services\GameOwnershipService;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart', methods: ['GET'])]
    public function index(CartService $cartService): Response
    {
        // get current user
        $user = $this->getUser();

        if ($user) {
            $cart = $cartService->getCart($user);
        } else {
            $cart = $cartService->getCart();
        }


        // dump($cart);
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'cart' => $cart
        ]);
    }

    #[Route('/cart/add/{id}/ajax', name: 'app_cart_add_ajax', methods: ['GET'])]
    public function add_ajax(int $id, CartService $cartService): Response
    {
        $result = $cartService->addToCart($id);
        return $this->sendResponse(['status' => $result[0], 'result' => $result[1]]);
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

    // add coupon then redirect to checkout
    #[Route('/cart/checkout/coupon', name: 'app_cart_checkout_coupon_post', methods: ['POST'])]
    public function checkout_coupon(CartService $cartService, Request $request, CouponRepository $couponRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cart = $cartService->getCart($user);

        if ($cart == null) {
            return $this->redirectToRoute('app_cart');
        }

        $formCoupon = $this->createForm(AddCouponType::class);

        $formCoupon->handleRequest($request);


        if ($formCoupon->isSubmitted() && $formCoupon->isValid()) {
            $couponName = $formCoupon->get('coupon')->getData();
            // get coupon from db by name
            $coupon = $couponRepository->findOneBy(['code' => $couponName]);
            // if coupon is valid
            if ($coupon != null) {
                $cartService->setCoupon($coupon);
            } else {
                $this->addFlash('error', 'Invalid coupon.');
            }
        }

        return $this->redirectToRoute('app_cart_checkout');
    }

    #[Route('/cart/checkout', name: 'app_cart_checkout', methods: ['GET'])]
    public function checkout(CartService $cartService): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cart = $cartService->getCart($user);

        if ($cart == null) {
            return $this->redirectToRoute('app_cart');
        }

        $dto = new CardVerificationDTO();

        $dto->cardOwner = $user->getFullName();

        $form = $this->createForm(CardVerificationType::class, $dto);

        $formCoupon = $this->createForm(AddCouponType::class);

        $total = $cartService->getCartPrice();

        // get taxes
        $taxePercent = 20;
        $taxes = $total * ($taxePercent / 100);
        $total += $taxes;

        return $this->render('cart/checkout.html.twig', [
            'controller_name' => 'CartController',
            'cart' => $cart,
            'taxes' => $taxes,
            'taxePercent' => $taxePercent,
            'total' => $total,
            'form' => $form->createView(),
            'formCoupon' => $formCoupon->createView()
        ]);
    }

    #[Route('/cart/checkout', name: 'app_cart_checkout_post', methods: ['POST'])]
    public function checkout_post(OrderService $orderService, CartService $cartService, PointsService $pointsService, BadgeService $badgeService, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cart = $cartService->getCart($user);

        if ($cart == null) {
            return $this->redirectToRoute('app_cart');
        }

        $dto = new CardVerificationDTO();

        $form = $this->createForm(CardVerificationType::class, $dto);

        $form->handleRequest($request);

        $formCoupon = $this->createForm(AddCouponType::class);

        $total = $cartService->getCartPrice();

        // get taxes
        $taxePercent = 20;
        $taxes = $total * ($taxePercent / 100);
        $total += $taxes;

        if ($form->isSubmitted() && $form->isValid()) {
            $order = $orderService->createOrder($cartService, $user, $total);
            $orderService->payOrder($order);

            $message = 'Your order N°' . $order->getId() . ' of ' . $total . '$ has been placed.';
            $this->addFlash('success', $message);

            $pointsService->addPoints(ceil($total * 10), $user);
            $badgeService->checkOnOrder($user);


            $cartService->clearCart();
            return $this->redirectToRoute('app_my_games');
        } else {
            $message = 'Payment failed.';
            // form errors
            $message .= ' ' . $form->getErrors(true, false);
            $this->addFlash('error', $message);
            return $this->render('cart/checkout.html.twig', [
                'controller_name' => 'CartController',
                'cart' => $cart,
                'taxes' => $taxes,
                'taxePercent' => $taxePercent,
                'total' => $total,
                'form' => $form->createView(),
                'formCoupon' => $formCoupon->createView()
            ]);
        }
    }

    // add coupon then redirect to checkout with get request
    #[Route('/cart/checkout/coupon/{coupon}', name: 'app_cart_checkout_coupon_get', methods: ['GET'])]
    public function checkout_coupon_get(CartService $cartService, CouponRepository $couponRepository, string $coupon): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cart = $cartService->getCart($user);

        if ($cart == null) {
            return $this->redirectToRoute('app_cart');
        }

        // get coupon from db by name
        $coupon = $couponRepository->findOneBy(['code' => $coupon]);
        // if coupon is valid
        if ($coupon != null) {
            $cartService->setCoupon($coupon);
        } else {
            $this->addFlash('error', 'Invalid coupon.');
        }

        return $this->redirectToRoute('app_cart_checkout');
    }
}
