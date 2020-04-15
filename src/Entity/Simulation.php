<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SimulationRepository")
 */
class Simulation
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
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Devis", inversedBy="simulations")
     * @ORM\JoinColumn(nullable=true)
     */
    private $devis;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Options", mappedBy="simulation")
     */
    private $options;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\RateCard", inversedBy="simulations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ratecard;

    public function __construct()
    {
        $this->options = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getDevis(): ?Devis
    {
        return $this->devis;
    }

    public function setDevis(?Devis $devis): self
    {
        $this->devis = $devis;

        return $this;
    }

    /**
     * @return Collection|Options[]
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(Options $option): self
    {
        if (!$this->options->contains($option)) {
            $this->options[] = $option;
            $option->addSimulation($this);
        }

        return $this;
    }

    public function removeOption(Options $option): self
    {
        if ($this->options->contains($option)) {
            $this->options->removeElement($option);
            $option->removeSimulation($this);
        }

        return $this;
    }

    public function getRatecard(): ?RateCard
    {
        return $this->ratecard;
    }

    public function setRatecard(?RateCard $ratecard): self
    {
        $this->ratecard = $ratecard;

        return $this;
    }
}
