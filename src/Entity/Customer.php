<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CustomerRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[UniqueEntity(fields: ['email', 'reseller'], message: 'mailUnique')]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Ignore()]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups('customer:read')]
    #[Assert\Regex('^[a-zA-Zéèàêùï-]{1,}+$^', message: 'firstNameIncorrect')]
    #[Assert\Length(max: 255, maxMessage: 'firstNameTooLong')]
    #[NotBlank(message: 'firstNameNotNull')]
    #[NotNull(message: 'firstNameNotNull')]
    private $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups('customer:read')]
    #[Assert\Regex('^[a-zA-Zéèàêùï-]{1,}+$^', message: 'lastNameIncorrect')]
    #[Assert\Length(max: 255)]
    #[NotBlank(message: 'lastNameNotNull')]
    #[NotNull(message: 'lastNameNotNull')]
    private $lastName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups('customer:read')]
    #[NotBlank()]
    #[NotNull()]
    private $adress;

    #[ORM\ManyToOne(targetEntity: Reseller::class, inversedBy: 'customers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('customer:read')]
    private $reseller;

    #[ORM\Column(type: 'uuid')]
    #[Groups('customer:read')]
    private $uuid;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups('customer:read')]
    #[Assert\Email(message: 'mailNotValid')]
    #[NotBlank(message: 'mailNotNull')]
    #[NotNull(message: 'mailNotNull')]
    private $email;

    #[ORM\Column(type: 'datetime')]
    #[Groups('customer:read')]
    private $createdAt;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getReseller(): ?Reseller
    {
        return $this->reseller;
    }

    public function setReseller(?Reseller $reseller): self
    {
        $this->reseller = $reseller;

        return $this;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function setUuid($uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
