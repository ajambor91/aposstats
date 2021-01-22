<?php

namespace App\Entity;

use App\Repository\ApostasyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ApostasyRepository::class)
 */
class Apostasy
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $ordinalNumber;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="integer")
     */
    private $apostasyYear;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $hash;

    /**
     * @ORM\Column(type="datetime")
     */
    private $scrappedAt;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="apostasies")
     */
    private $fittedCity;

    /**
     * @ORM\ManyToOne(targetEntity=Voivodeship::class, inversedBy="apostasies")
     */
    private $fittedVoivdeship;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrdinalNumber(): ?int
    {
        return $this->ordinalNumber;
    }

    public function setOrdinalNumber(int $ordinalNumber): self
    {
        $this->ordinalNumber = $ordinalNumber;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getApostasyYear(): ?int
    {
        return $this->apostasyYear;
    }

    public function setApostasyYear(int $apostasyYear): self
    {
        $this->apostasyYear = $apostasyYear;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getScrappedAt(): ?\DateTimeInterface
    {
        return $this->scrappedAt;
    }

    public function setScrappedAt(\DateTimeInterface $scrappedAt): self
    {
        $this->scrappedAt = $scrappedAt;

        return $this;
    }

    public function getFittedCity(): ?City
    {
        return $this->fittedCity;
    }

    public function setFittedCity(?City $fittedCity): self
    {
        $this->fittedCity = $fittedCity;

        return $this;
    }

    public function getFittedVoivdeship(): ?Voivodeship
    {
        return $this->fittedVoivdeship;
    }

    public function setFittedVoivdeship(?Voivodeship $fittedVoivdeship): self
    {
        $this->fittedVoivdeship = $fittedVoivdeship;

        return $this;
    }
}
