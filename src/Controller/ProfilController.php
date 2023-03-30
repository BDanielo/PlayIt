<?php

namespace App\Controller;

use App\DTO\ProfilDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ProfilType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfilController extends AbstractController
{
    #[Route('/user/profil', name: 'app_profil', methods: ['GET'])]
    public function index(): Response
    {
        if ($this->getUser()) {
            //** @var User $user */
            $user = $this->getUser();
            $user->setLastSigninDateTime(new \DateTime('now'));
            $userRepository->save($user, true);
        }
        
        //** @var User $user */
        $user = $this->getUser();

        $dto = new ProfilDTO();

        $dto->firstname = $user->getFirstname();
        $dto->lastname = $user->getLastname();
        $dto->email = $user->getEmail();
        $dto->address = $user->getAddress();
        $dto->username = $user->getUsername();

        $form = $this->createForm(ProfilType::class, $dto);

        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/profil', name: 'app_profil_post', methods: ['POST'])]
    public function update(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository): Response
    {
        //** @var User $user */
        $user = $this->getUser();

        $dto = new ProfilDTO();

        $dto->firstname = $user->getFirstname();
        $dto->lastname = $user->getLastname();
        $dto->email = $user->getEmail();
        $dto->address = $user->getAddress();
        $dto->username = $user->getUsername();

        $form = $this->createForm(ProfilType::class, $dto);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('error', 'testError2');

            $currentPassword = $dto->currentPassword;
            $newPassword = $dto->newPassword;
            $confirmNewPassword = $dto->confirmNewPassword;

            if ($userPasswordHasher->isPasswordValid($user, $currentPassword)) {
                if ($currentPassword && $newPassword && $confirmNewPassword) {
                    if ($newPassword === $confirmNewPassword) {
                        $user->setPassword($newPassword);
                    } else {
                        $this->addFlash('error', 'New password and confirm new password are not the same');
                    }
                }

                if ($dto->firstname !== $user->getFirstname()) {
                    $user->setFirstname($dto->firstname);
                }

                if ($dto->lastname !== $user->getLastname()) {
                    $user->setLastname($dto->lastname);
                }

                if ($dto->email !== $user->getEmail()) {
                    $user->setEmail($dto->email);
                }

                if ($dto->address !== $user->getAddress()) {
                    $user->setAddress($dto->address);
                }

                if ($dto->username !== $user->getUsername()) {
                    $user->setUsername($dto->username);
                }

                $userRepository->save($user, true);
            } else {
                $this->addFlash('error', 'Wrong password');
            }

            return $this->redirectToRoute('app_profil');
        } else {
            $this->addFlash('error', '' . $form->getErrors(true, false));
        }

        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
            'form' => $form->createView(),
        ]);
    }
}
