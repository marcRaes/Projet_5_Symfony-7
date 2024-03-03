<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Message;
use App\Form\CommentFormType;
use App\Form\MessageFormType;
use App\Repository\CommentRepository;
use App\Repository\MessageRepository;
use App\Service\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private MailerService $mailer;
    private MessageRepository $messageRepository;
    private CommentRepository $commentRepository;

    public function __construct(MailerService $mailer, MessageRepository $messageRepository, CommentRepository $commentRepository)
    {
        $this->messageRepository = $messageRepository;
        $this->commentRepository = $commentRepository;
        $this->mailer = $mailer;
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->redirectToRoute('home');
    }

    #[Route('/home', name: 'home')]
    public function home(Request $request): Response
    {
        $listCommentMessage = $this->messageRepository->findBy([], ['dateTimeRegistration' => 'DESC']);
        $message = new Message;
        $formAddMessage = $this->createForm(MessageFormType::class, $message);
        $formAddMessage->handleRequest($request);

        $formTab = [];
        foreach ($listCommentMessage as $liste) {
            $comment = new Comment;
            $formAddComment = $this->createForm(CommentFormType::class, $comment);
            $formTab[] = $formAddComment->createView();
            $formAddComment->handleRequest($request);
        }

        if ($formAddMessage->isSubmitted() && $formAddMessage->isValid()) {
            $message->setUser($this->getUser());
            $this->messageRepository->save($message, true);

            return $this->redirectToRoute('home');
        }

        if (isset($formAddComment) && $formAddComment->isSubmitted() && $formAddComment->isValid()) {
            $message = $this->messageRepository->findOneBy(['id' => $request->request->get('idMessage')]);
            $comment->setUser($this->getUser());
            $comment->setMessage($message);
            $this->commentRepository->save($comment, true);

            $this->mailer->sendCommentMail($comment);

            return $this->redirectToRoute('home');
        }

        return $this->render('home/index.html.twig', [
            'formAddMessage' => $formAddMessage->createView(),
            'formAddComment' => $formTab,
            'listCommentMessageUser' => $listCommentMessage,
        ]);
    }

    #[Route('/{id}', name: 'pageUser', requirements: ['id' => '\d+'])]
    public function messageUserAction(int $id, MessageRepository $messageRepository)
    {
        $listCommentMessageUser = $messageRepository->findBy(['user' => $id]);

        return $this->render('home/pageUser.html.twig', ['listCommentMessageUser' => $listCommentMessageUser,]);
    }
}
