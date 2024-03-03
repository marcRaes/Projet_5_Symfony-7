<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Address;

class MailerService
{
    private TransportInterface $mailer;
    private UserRepository $userRepository;

    public function __construct(TransportInterface $mailer, UserRepository $userRepository)
    {
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
    }

    public function sendRegisterMail(User $user): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('sosharcelnepasrepondre@marcraes.fr', 'SOS-Harcel'))
            ->to($user->getEmail())
            ->subject('Confirmation création de compte SOS Harcel')
            ->htmlTemplate('email/register.html.twig')
            ->context([
                'name' => $user->getPseudo(),
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $exception) {
            throw new TransportException($exception->getMessage());
        }
    }

    public function sendCommentMail(Comment $comment): void
    {
        $userMessage = $this->userRepository->findOneBy(['id' => $comment->getMessage()->getUser()->getId()]);
        $email = (new TemplatedEmail())
            ->from(new Address('sosharcelnepasrepondre@marcraes.fr', 'SOS-Harcel'))
            ->to($userMessage->getEmail())
            ->subject('Votre message contient un nouveau commentaire')
            ->htmlTemplate('email/addComment.html.twig')
            ->context([
                'mailComment' => $comment,
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $exception) {
            throw new TransportException($exception->getMessage());
        }
    }
}
