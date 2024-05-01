<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EventRepository::class)]

class Event
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
        message: 'Your name cannot contain a number',)]
        #[Groups(['search'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['search'])]
    private ?int $capacite = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 4,minMessage: "veuillez avoir au minimum 4 caractere" )]
    #[Groups(['search'])]
   
    private ?string $localization = null;

    #[ORM\Column(length: 255)]
    #[Groups(['search'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['search'])]
    private ?string $type= null;

    #[ORM\Column(length: 255)]
    #[Groups(['search'])]
    private ?string $image = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['search'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\OneToMany(targetEntity: Participatient::class, mappedBy: 'event')]
    private Collection $Participatient;

   

    public function __construct()
    {
        $this->Participatient = new ArrayCollection();
       
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): static
    {
        $this->capacite = $capacite;

        return $this;
    }

    public function getLocalization(): ?string
    {
        return $this->localization;
    }

    public function setLocalization(string $localization): static
    {
        $this->localization = $localization;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function gettype(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection<int, Participatient>
     */
    public function getParticipatient(): Collection
    {
        return $this->Participatient;
    }

    public function addParticipatient(Participatient $participatient): static
    {
        if (!$this->Participatient->contains($participatient)) {
            $this->Participatient->add($participatient);
            $participatient->setEvent($this);
        }

        return $this;
    }

    public function removeParticipatient(Participatient $participatient): static
    {
        if ($this->Participatient->removeElement($participatient)) {
            // set the owning side to null (unless already changed)
            if ($participatient->getEvent() === $this) {
                $participatient->setEvent(null);
            }
        }

        return $this;
    }
    public function __toString(): string
{
return $this->name ?? ''; // Assuming 'Name' is the property representing the club's name
}

}


