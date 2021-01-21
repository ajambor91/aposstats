<?php

namespace App\Entity;

use App\Repository\CityRepository;
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
}
