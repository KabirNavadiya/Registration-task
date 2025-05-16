<?php

namespace App\Entity;

use App\Repository\CompanyUserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CompanyUserRepository::class)
 */
class CompanyUser extends User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $companyName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $companyEmail;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getCompanyEmail(): ?string
    {
        return $this->companyEmail;
    }

    public function setCompanyEmail(?string $companyEmail): self
    {
        $this->companyEmail = $companyEmail;

        return $this;
    }
}
