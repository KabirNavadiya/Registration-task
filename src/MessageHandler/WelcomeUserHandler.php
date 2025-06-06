<?php

namespace App\MessageHandler;

use App\Message\WelcomeUser;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Address;

class WelcomeUserHandler implements MessageHandlerInterface
{
    private $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function __invoke(WelcomeUser $user)
    {
        $submittedUser = $user->getSubmittedUser();

//        Demonstrating Failure transport :-
//        if(rand(0,10) < 7 ){
//            throw new \Exception('I failed randomly!!');
//        }

        $email = (new TemplatedEmail())
            ->from(new Address('kabirnavadia27@gmail.com','The Library System'))
            ->to( new Address($submittedUser->getEmail(), $submittedUser->getUsername()) )
            ->subject('Welcome to Library System!')
            ->htmlTemplate('email/email.html.twig')
            ->context([
                'user' => $submittedUser,
            ])
        ;
        $this->mailer->send($email);
    }
}