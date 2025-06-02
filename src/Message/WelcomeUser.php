<?php

namespace App\Message;

class WelcomeUser
{
    private $user;

    public function __construct($user)
    {
        $this->user= $user;
    }

    public function getSubmittedUser()
    {
        return $this->user;
    }

}