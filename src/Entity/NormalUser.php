<?php

namespace App\Entity;

use App\Repository\NormalUserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NormalUserRepository::class)
 */
class NormalUser extends User
{

}
