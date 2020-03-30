<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FacturesRepository")
 */
class Factures
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="factures")
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\RateCard", mappedBy="facture", cascade={"persist", "remove"})
     */
    private $rateCard;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Simulation", mappedBy="factures", cascade={"persist", "remove"})
     */
    private $simulation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getRateCard(): ?RateCard
    {
        return $this->rateCard;
    }

    public function setRateCard(?RateCard $rateCard): self
    {
        $this->rateCard = $rateCard;

        // set (or unset) the owning side of the relation if necessary
        $newFacture = null === $rateCard ? null : $this;
        if ($rateCard->getFacture() !== $newFacture) {
            $rateCard->setFacture($newFacture);
        }

        return $this;
    }

    public function getSimulation(): ?Simulation
    {
        return $this->simulation;
    }

    public function setSimulation(?Simulation $simulation): self
    {
        $this->simulation = $simulation;

        // set (or unset) the owning side of the relation if necessary
        $newFactures = null === $simulation ? null : $this;
        if ($simulation->getFactures() !== $newFactures) {
            $simulation->setFactures($newFactures);
        }

        return $this;
    }
}
