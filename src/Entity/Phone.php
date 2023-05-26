<?php

namespace App\Entity;

use App\Repository\PhoneRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "phone_details",
 *          parameters = { "id" = "expr(object.getId())" }
 *      )
 * )
 *
 */
#[ORM\Entity(repositoryClass: PhoneRepository::class)]
#[UniqueEntity('label', message : 'Le modèle de téléphone est déjà existant.')]
class Phone
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: "Le modèle du téléphone est obligatoire.")]
    #[Assert\Length(min: 2, max: 255, minMessage: "Le nom du modèle doit faire au moins {{ limit }} caractères.", maxMessage: "Le nom du modèle ne peut pas faire plus de {{ limit }} caractères.")]
    private ?string $label = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "La marque du téléphone est obligatoire.")]
    #[Assert\Length(min: 1, max: 50, minMessage: "La marque du téléphone doit faire au moins {{ limit }} caractères.", maxMessage: "Le nom du modèle ne peut pas faire plus de {{ limit }} caractères.")]
    private ?string $brand = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le prix du téléphone est obligatoire.")]
    #[Assert\Regex(
        pattern: "/^\d+(\.\d{1,2})?$/",
        message: 'Le prix doit seulement contenir des chiffres et jusqu\'à deux décimales séparées par un point.'
    )]
    private ?float $price = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le système d'exploitation du téléphone est obligatoire.")]
    #[Assert\Length(min: 1, max: 50, minMessage: "Le système d'exploitation du téléphone doit faire au moins {{ limit }} caractères.", maxMessage: "Le système d'exploitation ne peut pas faire plus de {{ limit }} caractères.")]
    private ?string $os = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le processeur du téléphone est obligatoire.")]
    #[Assert\Length(min: 1, max: 50, minMessage: "Le processeur du téléphone doit faire au moins {{ limit }} caractères", maxMessage: "Le processeur du téléphone ne peut pas faire plus de {{ limit }} caractères.")]
    private ?string $cpu = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(min: 2, max: 255, minMessage: "Les infos sur l'écran du téléphone doivent faire au moins {{ limit }} caractères.", maxMessage: "Les infos sur l'écran du téléphone ne peuvent pas faire plus de {{ limit }} caractères.")]
    private ?string $screen = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La disponibilité du téléphone est obligatoire.")]
    private ?bool $isAvailable = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getOs(): ?string
    {
        return $this->os;
    }

    public function setOs(string $os): self
    {
        $this->os = $os;

        return $this;
    }

    public function getCpu(): ?string
    {
        return $this->cpu;
    }

    public function setCpu(string $cpu): self
    {
        $this->cpu = $cpu;

        return $this;
    }

    public function getScreen(): ?string
    {
        return $this->screen;
    }

    public function setScreen(?string $screen): self
    {
        $this->screen = $screen;

        return $this;
    }

    public function isIsAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): self
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }
}
