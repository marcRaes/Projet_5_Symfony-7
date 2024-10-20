<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Message;
use App\Form\CommentFormType;
use App\Form\MessageFormType;
use App\Repository\CommentRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->redirectToRoute('home');
    }

    #[Route('/home', name: 'home')]
    public function home(Request $request, MessageRepository $messageRepository, CommentRepository $commentRepository, MailerService $mailer): Response
    {
        $listCommentMessage = $messageRepository->findBy([], ['dateTimeRegistration' => 'DESC']);
        $message = new Message;
        $formAddMessage = $this->createForm(MessageFormType::class, $message);
        $formAddMessage->handleRequest($request);

        $formTab = [];
        $nbMessages = 0;
        while($nbMessages <= count($listCommentMessage)) {
            $nbMessages++;
            $comment = new Comment;
            $formAddComment = $this->createForm(CommentFormType::class, $comment);
            $formTab[] = $formAddComment->createView();
            $formAddComment->handleRequest($request);
        }

        if ($formAddMessage->isSubmitted() && $formAddMessage->isValid()) {
            $message->setUser($this->getUser());
            $messageRepository->save($message, true);

            return $this->redirectToRoute('home');
        }

        if (isset($formAddComment) && $formAddComment->isSubmitted() && $formAddComment->isValid()) {
            $message = $messageRepository->findOneBy(['id' => $request->request->get('idMessage')]);
            $comment->setUser($this->getUser());
            $comment->setMessage($message);
            $commentRepository->save($comment, true);

            $mailer->sendCommentMail($comment);

            return $this->redirectToRoute('home');
        }

        return $this->render('home/index.html.twig', [
            'formAddMessage' => $formAddMessage->createView(),
            'formAddComment' => $formTab,
            'listCommentMessageUser' => $listCommentMessage,
        ]);
    }

    #[Route('/{userId}', name: 'pageUser', requirements: ['userId' => '\d+'])]
    public function messageUserAction(int $userId, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        return $this->render('home/pageUser.html.twig', ['listCommentMessageUser' => $user->getMessages(),]);
    }
}
