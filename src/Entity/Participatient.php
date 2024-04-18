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
    #[Assert\Length(min: 4,minMessage: "veuillez avoir au minimum 4 caractere" )]


    #[Assert\Regex(
        pattern: '/\d/',
        match: false,
        message: 'Your prenom cannot contain a number',
    )]
    private ?string $nom_par = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 4,minMessage: "veuillez avoir au minimum 4 caractere" )]


    #[Assert\Regex(
        pattern: '/\d/',
        match: false,
        message: 'Your prenom cannot contain a number',
    )]
    private ?string $prenom_par = null;

    #[ORM\Column(length: 255)]

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
