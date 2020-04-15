<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RateCardRepository")
 */
class RateCard
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
    private $solution;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prestation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $models;

    /**
     * @ORM\Column(type="float")
     */
    private $priceRateCard;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Simulation", mappedBy="ratecard", orphanRemoval=true)
     */
    private $simulations;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $brand;

    public function __construct()
    {
        $this->simulations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSolution(): ?string
    {
        return $this->solution;
    }

    public function setSolution(string $solution): self
    {
        $this->solution = $solution;

        return $this;
    }

    public function getPrestation(): ?string
    {
        return $this->prestation;
    }

    public function setPrestation(string $prestation): self
    {
        $this->prestation = $prestation;

        return $this;
    }

    public function getModels(): ?string
    {
        return $this->models;
    }

    public function setModels(string $models): self
    {
        $this->models = $models;

        return $this;
    }

    public function getPriceRateCard(): ?float
    {
        return $this->priceRateCard;
    }

    public function setPriceRateCard(float $priceRateCard): self
    {
        $this->priceRateCard = $priceRateCard;

        return $this;
    }

    /**
     * @return Collection|Simulation[]
     */
    public function getSimulations(): Collection
    {
        return $this->simulations;
    }

    public function addSimulation(Simulation $simulation): self
    {
        if (!$this->simulations->contains($simulation)) {
            $this->simulations[] = $simulation;
            $simulation->setRatecard($this);
        }

        return $this;
    }

    public function removeSimulation(Simulation $simulation): self
    {
        if ($this->simulations->contains($simulation)) {
            $this->simulations->removeElement($simulation);
            // set the owning side to null (unless already changed)
            if ($simulation->getRatecard() === $this) {
                $simulation->setRatecard(null);
            }
        }

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function __toString()
    {
        return $this->brand;
        // TODO: Implement __toString() method.
    }
}
