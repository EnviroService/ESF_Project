<?php

namespace App\Entity;

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
     * @ORM\Column(type="integer")
     */
    private $priceRateCard;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Factures", inversedBy="rateCard", cascade={"persist", "remove"})
     */
    private $facture;

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

    public function getPriceRateCard(): ?int
    {
        return $this->priceRateCard;
    }

    public function setPriceRateCard(int $priceRateCard): self
    {
        $this->priceRateCard = $priceRateCard;

        return $this;
    }

    public function getFacture(): ?Factures
    {
        return $this->facture;
    }

    public function setFacture(?Factures $facture): self
    {
        $this->facture = $facture;

        return $this;
    }
}
