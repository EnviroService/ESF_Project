<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OptionsRepository")
 */
class Options
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $priceOption;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Simulation", inversedBy="options")
     */
    private $simulation;

    public function __construct()
    {
        $this->simulation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPriceOption(): ?int
    {
        return $this->priceOption;
    }

    public function setPriceOption(int $priceOption): self
    {
        $this->priceOption = $priceOption;

        return $this;
    }

    /**
     * @return Collection|Simulation[]
     */
    public function getSimulation(): Collection
    {
        return $this->simulation;
    }

    public function addSimulation(Simulation $simulation): self
    {
        if (!$this->simulation->contains($simulation)) {
            $this->simulation[] = $simulation;
        }

        return $this;
    }

    public function removeSimulation(Simulation $simulation): self
    {
        if ($this->simulation->contains($simulation)) {
            $this->simulation->removeElement($simulation);
        }

        return $this;
    }
}
