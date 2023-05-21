<?php

namespace App\Entity;

use ORM\Table;
use OA\Property;
use ORM\UniqueConstraint;
use OpenApi\Annotations as OA;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use App\Repository\CustomerRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "customer_details",
 *          parameters = { "customer_id" = "expr(object.getId())" }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="getCustomerDetails")
 * )
 *
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "delete_customer",
 *          parameters = { "customer_id" = "expr(object.getId())" }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="getCustomerDetails", excludeIf = "expr(not is_granted('ROLE_ADMIN'))")
 * )
 *
 * @Hateoas\Relation(
 *      "update",
 *      href = @Hateoas\Route(
 *          "update_customer",
 *          parameters = { "customer_id" = "expr(object.getId())" }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="getCustomerDetails", excludeIf = "expr(not is_granted('ROLE_ADMIN'))")
 * )
 *
 */
#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\Table(name: 'customer')]
#[ORM\UniqueConstraint(name: 'unique_email_marketplace_idx', columns: ['email', 'marketplace_id'])]
#[UniqueEntity(fields: ["email", "marketplace"], message : 'L\'email est déjà utilisé par un autre client.')]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getCustomerDetails"])]
    private ?int $id = null;

    /**
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     example="string"
     * )
     * 
     */
    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le nom du client est obligatoire.")]
    #[Assert\Length(min: 2, max: 50, minMessage: "Le nom de l'utilisateur doit faire au moins {{ limit }} caractères.", maxMessage: "Le nom de l'utilisateur ne peut pas faire plus de {{ limit }} caractères.")]
    #[Assert\Regex(
        pattern: "/^([a-zA-Z']{2,50})$/",
        message: 'Le nom doit seulement contenir des lettres.'
    )]
    #[Groups(["getCustomerDetails"])]
    private ?string $name = null;

    /**
     * @OA\Property(
     *     property="nickname",
     *     type="string",
     *     example="string"
     * )
     * 
     */
    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le prénom du client est obligatoire.")]
    #[Assert\Length(min: 2, max: 50, minMessage: "Le prénom de l'utilisateur doit faire au moins {{ limit }} caractères.", maxMessage: "Le prénom de l'utilisateur ne peut pas faire plus de {{ limit }} caractères.")]
    #[Assert\Regex(
        pattern: "/^([a-zA-Z']{2,50})$/",
        message: 'Le prénom doit seulement contenir des lettres.'
    )]
    #[Groups(["getCustomerDetails"])]
    private ?string $nickname = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le mail doit être renseigné.')]
    #[Assert\Email(message: 'Le format de l\'email n\'est pas valide.',)]
    #[Assert\Length(max: 255, maxMessage: "L'email ne peut pas faire plus de {{ limit }} caractères.")]
    #[Groups(["getCustomerDetails"])]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'customers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $marketplace = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: "L'adresse ne peut pas faire plus de {{ limit }} caractères.")]
    #[Groups(["getCustomerDetails"])]
    private ?string $adress = null;

    public function update(?Customer $updateCustomer): self
    {
        $this->name = $updateCustomer->getName();
        $this->nickname = $updateCustomer->getNickname();
        $this->email = $updateCustomer->getEmail();
        $this->adress = $updateCustomer->getAdress();

        return $this;
    }

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
