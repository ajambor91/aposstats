<?php

namespace App\Entity;

use App\Repository\VoivodeshipRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VoivodeshipRepository::class)
 */
class Voivodeship
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
     * @ORM\OneToMany(targetEntity=City::class, mappedBy="voivodeship", cascade={"persist"})
     */
    private $cities;

    /**
     * @ORM\OneToMany(targetEntity=Apostasy::class, mappedBy="fittedVoivdeship")
     */
    private $apostasies;

    public function __construct()
    {
        $this->cities = new ArrayCollection();
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

    /**
     * @return Collection|City[]
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    public function addCity(City $city): self
    {
        if (!$this->cities->contains($city)) {
            $this->cities[] = $city;
            $city->setVoivodeship($this);
        }

        return $this;
    }

    public function removeCity(City $city): self
    {
        if ($this->cities->removeElement($city)) {
            // set the owning side to null (unless already changed)
            if ($city->getVoivodeship() === $this) {
                $city->setVoivodeship(null);
            }
        }

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
            $apostasy->setFittedVoivdeship($this);
        }

        return $this;
    }

    public function removeApostasy(Apostasy $apostasy): self
    {
        if ($this->apostasies->removeElement($apostasy)) {
            // set the owning side to null (unless already changed)
            if ($apostasy->getFittedVoivdeship() === $this) {
                $apostasy->setFittedVoivdeship(null);
            }
        }

        return $this;
    }
}
