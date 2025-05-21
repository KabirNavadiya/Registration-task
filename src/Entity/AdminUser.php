<?php

namespace App\Entity;

use App\Repository\CompanyUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=CompanyUserRepository::class)
 */
class AdminUser extends User
{

}
