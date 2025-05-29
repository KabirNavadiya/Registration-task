<?php

namespace App\Command;

use App\Event\SendOverdueMailEvent;
use App\Repository\LoanRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SendOverdueMailCommand extends Command
{
    protected static $defaultName = 'app:send-overdue-mail';
    protected static $defaultDescription = 'Send an email to users how has overdue books';

    private EventDispatcherInterface $dispatcher;
    private LoanRepository $loanRepository;

    public function __construct(EventDispatcherInterface $dispatcher,LoanRepository $loanRepository)
    {
        parent::__construct();
        $this->dispatcher = $dispatcher;
        $this->loanRepository = $loanRepository;

    }
    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
//        $arg1 = $input->getArgument('arg1');
//
//        if ($arg1) {
//            $io->note(sprintf('You passed an argument: %s', $arg1));
//        }
//
//        if ($input->getOption('option1')) {
//            // ...
//        }
        $this->dispatcher->dispatch(new SendOverdueMailEvent($this->loanRepository),SendOverdueMailEvent::SEND_MAIL);

        $io->success('Email sent. ');

        return Command::SUCCESS;
    }
}
