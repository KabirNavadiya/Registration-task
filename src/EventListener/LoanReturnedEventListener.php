<?php

namespace App\EventListener;

use App\Event\LoanReturnedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoanReturnedEventListener implements EventSubscriberInterface
{

    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }
    public static function getSubscribedEvents()
    {
        return [
            LoanReturnedEvent::LOAN_RETURNED => 'onLoanReturned',
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

}