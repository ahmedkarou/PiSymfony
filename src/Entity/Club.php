<?php

namespace App\Entity;

use App\Repository\ClubRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClubRepository::class)]
class Club
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 4,minMessage: "veuillez avoir au minimum 4 caractere" )]
    #[Assert\Regex(
        pattern: '/\d/',
        match: false,
        message: 'Your name cannot contain a number',)]
    private ?string $Name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 4,minMessage: "veuillez avoir au minimum 4 caractere" )]


    #[Assert\Regex(
        pattern: '/\d/',
        match: false,
        message: 'Your prenom cannot contain a number',
    )]
    private ?string $organizer = null;

    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[ORM\Column]
    private ?int $capacity = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

   

    #[ORM\Column(length: 255)]
    private ?string $descripton = null;
    #[ORM\Column(type: 'string')]
    private string $image;

    #[ORM\OneToMany(targetEntity: Offre::class, mappedBy: 'club',cascade: ['persist','remove'])]
    private Collection $offer;

    #[ORM\ManyToOne(inversedBy: 'club')]
    private ?Inscription $inscription = null;

    public function __construct()
    {
        $this->offer = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): static
    {
        $this->Name = $Name;

        return $this;
    }

    public function getOrganizer(): ?string
    {
        return $this->organizer;
    }

    public function setOrganizer(string $organizer): static
    {
        $this->organizer = $organizer;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    

    public function getDescripton(): ?string
    {
        return $this->descripton;
    }

    public function setDescripton(string $descripton): static
    {
        $this->descripton = $descripton;

        return $this;
    }

    /**
     * @return Collection<int, Offre>
     */
    public function getOffer(): Collection
    {
        return $this->offer;
    }

    public function addOffer(Offre $offer): static
    {
        if (!$this->offer->contains($offer)) {
            $this->offer->add($offer);
            $offer->setClub($this);
        }

        return $this;
    }

    public function removeOffer(Offre $offer): static
    {
        if ($this->offer->removeElement($offer)) {
            // set the owning side to null (unless already changed)
            if ($offer->getClub() === $this) {
                $offer->setClub(null);
            }
        }

        return $this;
    }
    public function __toString(): string
{
    return $this->Name ?? ''; // Assuming 'Name' is the property representing the club's name
}
public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getInscription(): ?Inscription
    {
        return $this->inscription;
    }

    public function setInscription(?Inscription $inscription): static
    {
        $this->inscription = $inscription;

        return $this;
    }
}
