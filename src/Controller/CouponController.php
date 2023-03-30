<?php

namespace App\Controller;

use App\DTO\CreateCouponDTO;
use App\Form\CreateCouponType;
use App\Entity\Coupon;
use App\Repository\CouponRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CouponController extends AbstractController
{
    #[Route('/admin/coupon', name: 'app_coupon')]
    public function index(CouponRepository $couponRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('You are not allowed to access this page');
        }

        // get all coupons
        $coupons = $couponRepository->findAll();

        return $this->render('coupon/index.html.twig', [
            'controller_name' => 'CouponController',
            'coupons' => $coupons,
        ]);
    }

    #[Route('/admin/coupon/new', name: 'app_coupon_new')]
    public function new(Request $request, CouponRepository $couponRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('You are not allowed to access this page');
        }

        // create new CreateCouponType form using DTO
        $dto = new CreateCouponDTO();
        $form = $this->createForm(CreateCouponType::class, $dto);

        // handle form submission
        $form->handleRequest($request);

        // if form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // create new coupon
            $coupon = new Coupon();
            $coupon->setCode($dto->code);
            $coupon->setPercent($dto->percent);
            $coupon->setStartDate($dto->startDate);
            $coupon->setEndDate($dto->endDate);

            // save coupon to database
            $couponRepository->save($coupon, true);

            // redirect to coupon index
            return $this->redirectToRoute('app_coupon');
        }


        return $this->render('coupon/new.html.twig', [
            'controller_name' => 'CouponController',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/coupon/{id}/edit', name: 'app_coupon_edit')]
    public function edit(int $id, Request $request, CouponRepository $couponRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('You are not allowed to access this page');
        }

        $coupon = $couponRepository->find($id);

        if (!$coupon) {
            throw $this->createNotFoundException('Coupon not found');
        }

        $dto = new CreateCouponDTO();

        $form = $this->createForm(CreateCouponType::class, $dto);

        // handle form submission
        $form->handleRequest($request);

        // if form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // update coupon
            $coupon->setCode($dto->code);
            $coupon->setPercent($dto->percent);
            $coupon->setStartDate($dto->startDate);
            $coupon->setEndDate($dto->endDate);

            // save coupon to database
            $couponRepository->save($coupon, true);

            // redirect to coupon index
            return $this->redirectToRoute('app_coupon');
        }

        // set form values
        $dto->code = $coupon->getCode();
        $dto->percent = $coupon->getPercentWithoutChecking();
        $dto->startDate = $coupon->getStartDate();
        $dto->endDate = $coupon->getEndDate();

        // refresh form
        $form = $this->createForm(CreateCouponType::class, $dto);



        return $this->render('coupon/edit.html.twig', [
            'controller_name' => 'CouponController',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/coupon/{id}/delete', name: 'app_coupon_delete')]
    public function delete(int $id, Request $request, CouponRepository $couponRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('You are not allowed to access this page');
        }

        $id = $request->get('id');

        $coupon = $couponRepository->find($id);

        if (!$coupon) {
            throw $this->createNotFoundException('Coupon not found');
        }

        $couponRepository->remove($coupon, true);

        return $this->redirectToRoute('app_coupon');
    }
}
