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
     * @ORM\OneToMany(targetEntity="App\Entity\RateCard", mappedBy="simulation", orphanRemoval=true)
     */
    private $rateCard;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Devis", inversedBy="simulations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $devis;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Options", mappedBy="simulation")
     */
    private $options;

    public function __construct()
    {
        $this->rateCard = new ArrayCollection();
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

    /**
     * @return Collection|RateCard[]
     */
    public function getRateCard(): Collection
    {
        return $this->rateCard;
    }

    public function addRateCard(RateCard $rateCard): self
    {
        if (!$this->rateCard->contains($rateCard)) {
            $this->rateCard[] = $rateCard;
            $rateCard->setSimulation($this);
        }

        return $this;
    }

    public function removeRateCard(RateCard $rateCard): self
    {
        if ($this->rateCard->contains($rateCard)) {
            $this->rateCard->removeElement($rateCard);
            // set the owning side to null (unless already changed)
            if ($rateCard->getSimulation() === $this) {
                $rateCard->setSimulation(null);
            }
        }

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
}
