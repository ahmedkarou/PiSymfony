<?php

namespace App\Entity;

use App\Repository\ParticipatientRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ParticipatientRepository::class)]
class Participatient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 4, minMessage: "Veuillez avoir au minimum 4 caractères.")]
    #[Assert\Regex(
        pattern: '/^\pL+$/u',
        message: 'Votre nom ne peut contenir que des lettres.'
    )]
    private ?string $nom_par = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 4, minMessage: "Veuillez avoir au minimum 4 caractères.")]
    #[Assert\Regex(
        pattern: '/^\pL+$/u',
        message: 'Votre prénom ne peut contenir que des lettres.'
    )]
    private ?string $prenom_par = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Type("numeric", message: "L'âge doit être un nombre.")]
    #[Assert\Range(
        min: 5,
        max: 16,
        minMessage: "L'âge doit être supérieur ou égal à 5.",
        maxMessage: "L'âge doit être inférieur ou égal à 16."
    )]
    private ?string $age_par = null;

    #[ORM\ManyToOne(inversedBy: 'Participatient')]
    private ?Event $event = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPar(): ?string
    {
        return $this->nom_par;
    }

    public function setNomPar(string $nom_par): static
    {
        $this->nom_par = $nom_par;

        return $this;
    }

    public function getPrenomPar(): ?string
    {
        return $this->prenom_par;
    }

    public function setPrenomPar(string $prenom_par): static
    {
        $this->prenom_par = $prenom_par;

        return $this;
    }

    public function getAgePar(): ?string
    {
        return $this->age_par;
    }

    public function setAgePar(string $age_par): static
    {
        $this->age_par = $age_par;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;

        return $this;
    }
}
