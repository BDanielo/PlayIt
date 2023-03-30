<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FriendController extends AbstractController
{
    #[Route('/user/friend', name: 'app_friend')]
    public function index(): Response
    {
        // get current user
        $user = $this->getUser();

        // get all friends of current user
        $friends = $user->getFriends();
        // dump($friends);
        return $this->render('friend/index.html.twig', [
            'controller_name' => 'FriendController',
            'friends' => $friends,
        ]);
    }
}
