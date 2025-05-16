<?php

namespace App\Entity;

use App\Repository\NormalUserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NormalUserRepository::class)
 */
class NormalUser extends User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isMember = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsMember(): ?bool
    {
        return $this->isMember;
    }

    public function setIsMember(bool $isMember): self
    {
        $this->isMember = $isMember;

        return $this;
    }
}
