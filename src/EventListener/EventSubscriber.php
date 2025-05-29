<?php

namespace App\EventListener;

use App\Event\LoanReturnedEvent;
use App\Event\SendOverdueMailEvent;
use App\Repository\LoanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class EventSubscriber implements EventSubscriberInterface
{

    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    private MailerInterface $mailer;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->mailer = $mailer;
    }
    public static function getSubscribedEvents()
    {
        return [
            LoanReturnedEvent::LOAN_RETURNED => 'onLoanReturned',
            SendOverdueMailEvent::SEND_MAIL => 'onSendOverdueMail',
        ];
    }

    public function onLoanReturned(LoanReturnedEvent $event)
    {
        $loan = $event->getLoan();
        $loan->setReturnedAt(new \DateTimeImmutable());
        $loan->getBook()->setIsAvailable(true);
        $this->entityManager->flush();
        $this->logger->info("The loan #{$loan->getId()} has been returned.");
    }

    public function onSendOverdueMail(SendOverdueMailEvent $event){
        $nonreturnables = $event->getUsersWithNotReturnedLoan();
        $groupedarray = [];
        $overdueuser=[];
        foreach ($nonreturnables as $key => $value) {
            $groupedarray[$value['email']][$key] = $value;
        }

        foreach ($groupedarray as $row) {
            $count = 0;
            foreach ($row as $key) {
                $interval = $key['dueAt']->diff($key['loanedAt']);
                $days = $interval->days;
                if ($days >= 25) {
                    $count++;
                }
            }
            if ($count >= 2) {
                array_push($overdueuser, $key['email']);
            }
        }

        foreach ($overdueuser as $user) {

            $email = (new TemplatedEmail())
                ->from(new Address('kabirnavadia27@gmail.com','The Library System'))
                ->to($user)
                ->subject('Return Loaned Books')
                ->htmlTemplate('email/return_loaned_books.html.twig');

            $this->mailer->send($email);
        }

    }

}