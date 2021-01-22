<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CityRepository::class)
 */
class City
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Voivodeship::class, inversedBy="cities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $voivodeship;

    /**
     * @ORM\OneToMany(targetEntity=Apostasy::class, mappedBy="fittedCity")
     */
    private $apostasies;

    public function __construct()
    {
        $this->apostasies = new ArrayCollection();
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

    public function getVoivodeship(): ?Voivodeship
    {
        return $this->voivodeship;
    }

    public function setVoivodeship(?Voivodeship $voivodeship): self
    {
        $this->voivodeship = $voivodeship;

        return $this;
    }

    /**
     * @return Collection|Apostasy[]
     */
    public function getApostasies(): Collection
    {
        return $this->apostasies;
    }

    public function addApostasy(Apostasy $apostasy): self
    {
        if (!$this->apostasies->contains($apostasy)) {
            $this->apostasies[] = $apostasy;
            $apostasy->setFittedCity($this);
        }

        return $this;
    }

    public function removeApostasy(Apostasy $apostasy): self
    {
        if ($this->apostasies->removeElement($apostasy)) {
            // set the owning side to null (unless already changed)
            if ($apostasy->getFittedCity() === $this) {
                $apostasy->setFittedCity(null);
            }
        }

        return $this;
    }
}
