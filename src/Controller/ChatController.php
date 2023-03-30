<?php

namespace App\Controller;

use App\Entity\ChatMessage;
use App\Repository\ChatMessageRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\NewChatMessageType;

class ChatController extends AbstractController
{
    #[Route('/user/friend/{id}/chat', name: 'app_chat')]
    public function index(int $id, UserRepository $userRepository, ChatMessageRepository $chatMessageRepository): Response
    {
        // get current user
        $user = $this->getUser();

        // get user with id $id using UserRepository
        $friend = $userRepository->find($id);

        // new form NewChatMessageType
        $form = $this->createForm(NewChatMessageType::class);

        // get all messages sended by current user to friend
        $messagesSended = $chatMessageRepository->findBy([
            'Sender' => $user,
            'Receiver' => $friend,
        ]);

        // get all messages received by current user from friend
        $messagesReceived = $chatMessageRepository->findBy([
            'Sender' => $friend,
            'Receiver' => $user,
        ]);

        // fusion of messagesSended and messagesReceived
        $messages = array_merge($messagesSended, $messagesReceived);
        // order messages by creationDate
        usort($messages, function ($a, $b) {
            return $a->getCreationDate() <=> $b->getCreationDate();
        });

        return $this->render('chat/index.html.twig', [
            'controller_name' => 'ChatController',
            'friend' => $friend,
            'messages' => $messages,
            'form' => $form->createView(),
        ]);
    }
    // send message to friend
    #[Route('/user/friend/{id}/chat/send/{message}', name: 'app_chat_send_get', methods: ['GET'])]
    public function sendMessageGet(int $id, UserRepository $userRepository, Request $request, ChatMessageRepository $chatMessageRepository, string $message): Response
    {
        // get current user
        $user = $this->getUser();

        // get user with id $id using UserRepository
        $friend = $userRepository->find($id);

        // create new ChatMessage
        $chatMessage = new ChatMessage();
        $chatMessage->setSender($user);
        $chatMessage->setReceiver($friend);
        $chatMessage->setText($message);
        $chatMessage->setCreationDate(new \DateTime());

        // save ChatMessage to database using ChatMessageRepository
        $chatMessageRepository->save($chatMessage, true);

        // redirect to chat page
        return $this->redirectToRoute('app_chat', [
            'id' => $friend->getId(),
        ]);
    }

    // same with post method
    #[Route('/user/friend/{id}/chat/send', name: 'app_chat_send_post', methods: ['POST'])]
    public function sendMessagePost(int $id, UserRepository $userRepository, Request $request, ChatMessageRepository $chatMessageRepository): Response
    {
        // get current user
        $user = $this->getUser();

        // get user with id $id using UserRepository
        $friend = $userRepository->find($id);

        // new form NewChatMessageType
        $form = $this->createForm(NewChatMessageType::class);

        // handle request
        $form->handleRequest($request);

        // if form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // get data from form
            $data = $form->getData();

            // create new ChatMessage
            $chatMessage = new ChatMessage();
            $chatMessage->setSender($user);
            $chatMessage->setReceiver($friend);
            $chatMessage->setText($data['message']);
            $chatMessage->setCreationDate(new \DateTime());

            // save ChatMessage to database using ChatMessageRepository
            $chatMessageRepository->save($chatMessage, true);
        }

        // redirect to chat page
        return $this->redirectToRoute('app_chat', [
            'id' => $friend->getId(),
        ]);
    }
}
