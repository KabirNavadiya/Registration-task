<?php

namespace App\Event;

use App\Repository\LoanRepository;

class SendOverdueMailEvent
{
    public const SEND_MAIL = 'send.overdue.mail';

    private LoanRepository $loanRepository;
    public function __construct(LoanRepository $loanRepository)
    {
        $this->loanRepository = $loanRepository;
    }
    public function getUsersWithNotReturnedLoan()
    {
        return $this->loanRepository->getNotReturnedLoans();
    }
}