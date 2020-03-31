<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DevisRepository")
 */
class Devis
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="devis")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Simulation", mappedBy="devis", orphanRemoval=true)
     */
    private $simulations;

    public function __construct()
    {
        $this->simulations = new ArrayCollection();
    }

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
            $simulation->setDevis($this);
        }

        return $this;
    }

    public function removeSimulation(Simulation $simulation): self
    {
        if ($this->simulations->contains($simulation)) {
            $this->simulations->removeElement($simulation);
            // set the owning side to null (unless already changed)
            if ($simulation->getDevis() === $this) {
                $simulation->setDevis(null);
            }
        }

        return $this;
    }
}
