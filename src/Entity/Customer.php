<?php

namespace App\Entity;

use ORM\Table;
use ORM\UniqueConstraint;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use App\Repository\CustomerRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[UniqueEntity(fields: ["email", "marketplace"], message : 'L\'email est déjà utilisé par un autre client.')]
// #[ORM\Table(
//     uniqueConstraints: [
//         #[UniqueConstraint(
//             name: "unique_email_marketplace",
//             columns: ['email', 'marketplace']
//         )]
//     ]
// )]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getCustomers", "getCustomerDetails"])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le nom de l'utilisateur est obligatoire.")]
    #[Assert\Length(min: 2, max: 50, minMessage: "Le nom de l'utilisateur doit faire au moins {{ limit }} caractères.", maxMessage: "Le nom de l'utilisateur ne peut pas faire plus de {{ limit }} caractères.")]
    #[Assert\Regex(
        pattern: "/^([a-zA-Z']{2,50})$/",
        message: 'Le nom doit seulement contenir des lettres.'
    )]
    #[Groups(["getCustomers", "getCustomerDetails"])]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le prénom de l'utilisateur est obligatoire.")]
    #[Assert\Length(min: 2, max: 50, minMessage: "Le prénom de l'utilisateur doit faire au moins {{ limit }} caractères.", maxMessage: "Le prénom de l'utilisateur ne peut pas faire plus de {{ limit }} caractères.")]
    #[Assert\Regex(
        pattern: "/^([a-zA-Z']{2,50})$/",
        message: 'Le prénom doit seulement contenir des lettres.'
    )]
    #[Groups(["getCustomers", "getCustomerDetails"])]
    private ?string $nickname = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'Le mail doit être renseigné.')]
    #[Assert\Email(message: 'Le format de l\'email n\'est pas valide.',)]
    #[Assert\Length(max: 255, maxMessage: "L'email ne peut pas faire plus de {{ limit }} caractères.")]
    #[Groups(["getCustomers", "getCustomerDetails"])]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'customers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $marketplace = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: "L'adresse ne peut pas faire plus de {{ limit }} caractères.")]
    #[Groups(["getCustomers", "getCustomerDetails"])]
    private ?string $adress = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

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

    public function getMarketplace(): ?User
    {
        return $this->marketplace;
    }

    public function setMarketplace(?User $marketplace): self
    {
        $this->marketplace = $marketplace;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(?string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }
}
