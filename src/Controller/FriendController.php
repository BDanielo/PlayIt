<?php

namespace App\Controller;

use App\DTO\AddFriendDTO;
use App\Form\NewChatMessageType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User;
use App\Form\AddFriendType;
use App\Form\Type\TaskType;

class FriendController extends AbstractController
{
    #[Route('/user/friend', name: 'app_friend', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        // get current user
        $user = $this->getUser();

        // get all friends of current user
        $friends = $user->getFriends();


        // using findAllExceptCurrentUser method from UserRepository
        $users = $userRepository->findAllExceptCurrentUser($user->getId());
        // dump($users);
        // dump($friends);
        // create form AddFriendType


        $form = $this->createForm(AddFriendType::class, [
            'users' => $users,
        ]);

        return $this->render('friend/index.html.twig', [
            'controller_name' => 'FriendController',
            'friends' => $friends,
            'form' => $form->createView()
        ]);
    }

    #[Route('/user/friend/add', name: 'app_friend_add', methods: ['POST'])]
    public function addFriend(UserRepository $repo, Request $request): Response
    {
        // get current user
        $user = $this->getUser();

        // get all friends of current user
        $friends = $user->getFriends();

        // $dto = new AddFriendDTO();
        $users = $repo->findAllExceptCurrentUser($user->getId());

        $form = $this->createForm(AddFriendType::class, [
            'users' => $users,
        ]);

        // handle request
        $form->handleRequest($request);

        // check if form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // get data from form
            $data = $form->getData();
            // dump($data);

            // get friend from database
            $friend = $repo->find($data['user']);
            // dump($friend);

            // add friend to current user
            $user->addFriendSended($friend);

            // add friendReceived to friend
            $friend->addFriendReceived($user);
            // dump($user);

            // save changes to database using userRepo
            $repo->save($user, true);

            // redirect to friend page
            return $this->redirectToRoute('app_friend');
        }

        return $this->render('friend/index.html.twig', [
            'controller_name' => 'FriendController',
            'friends' => $friends,
            'form' => $form->createView()
        ]);
    }
}
