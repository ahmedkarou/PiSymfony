<?php

namespace App\Entity;

use App\Repository\ClubRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClubRepository::class)]
class Club
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\Column(length: 255)]
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

    #[ORM\OneToMany(targetEntity: Offre::class, mappedBy: 'club')]
    private Collection $offer;

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
}
