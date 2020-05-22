<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TrackingRepository")
 */
class Tracking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("booking")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("booking")
     */
    private $imei;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isSent;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $sentDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isReceived;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $receivedDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRepaired;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $repairedDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isReturned;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $returnedDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Booking", inversedBy="trackings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booking;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Solution", mappedBy="tracking")
     */
    private $solutions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Option", mappedBy="tracking")
     */
    private $options;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Simulation", inversedBy="trackings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $simulation;

    public function __construct()
    {
        $this->solutions = new ArrayCollection();
        $this->options = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImei(): ?string
    {
        return $this->imei;
    }

    public function setImei(string $imei): self
    {
        $this->imei = $imei;

        return $this;
    }

    public function getIsSent(): ?bool
    {
        return $this->isSent;
    }

    public function setIsSent(bool $isSent): self
    {
        $this->isSent = $isSent;

        return $this;
    }

    public function getSentDate(): ?\DateTimeInterface
    {
        return $this->sentDate;
    }

    public function setSentDate(?\DateTimeInterface $sentDate): self
    {
        $this->sentDate = $sentDate;

        return $this;
    }

    public function getIsReceived(): ?bool
    {
        return $this->isReceived;
    }

    public function setIsReceived(bool $isReceived): self
    {
        $this->isReceived = $isReceived;

        return $this;
    }

    public function getReceivedDate(): ?\DateTimeInterface
    {
        return $this->receivedDate;
    }

    public function setReceivedDate(?\DateTimeInterface $receivedDate): self
    {
        $this->receivedDate = $receivedDate;

        return $this;
    }

    public function getIsRepaired(): ?bool
    {
        return $this->isRepaired;
    }

    public function setIsRepaired(bool $isRepaired): self
    {
        $this->isRepaired = $isRepaired;

        return $this;
    }

    public function getRepairedDate(): ?\DateTimeInterface
    {
        return $this->repairedDate;
    }

    public function setRepairedDate(?\DateTimeInterface $repairedDate): self
    {
        $this->repairedDate = $repairedDate;

        return $this;
    }

    public function getIsReturned(): ?bool
    {
        return $this->isReturned;
    }

    public function setIsReturned(bool $isReturned): self
    {
        $this->isReturned = $isReturned;

        return $this;
    }

    public function getReturnedDate(): ?\DateTimeInterface
    {
        return $this->returnedDate;
    }

    public function setReturnedDate(?\DateTimeInterface $returnedDate): self
    {
        $this->returnedDate = $returnedDate;

        return $this;
    }

    public function getBooking(): ?Booking
    {
        return $this->booking;
    }

    public function setBooking(?Booking $booking): self
    {
        $this->booking = $booking;

        return $this;
    }

    /**
     * @return Collection|Solution[]
     */
    public function getSolutions(): Collection
    {
        return $this->solutions;
    }

    public function addSolution(Solution $solution): self
    {
        if (!$this->solutions->contains($solution)) {
            $this->solutions[] = $solution;
            $solution->setTracking($this);
        }

        return $this;
    }

    public function removeSolution(Solution $solution): self
    {
        if ($this->solutions->contains($solution)) {
            $this->solutions->removeElement($solution);
            // set the owning side to null (unless already changed)
            if ($solution->getTracking() === $this) {
                $solution->setTracking(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Option[]
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(Option $option): self
    {
        if (!$this->options->contains($option)) {
            $this->options[] = $option;
            $option->setTracking($this);
        }

        return $this;
    }

    public function removeOption(Option $option): self
    {
        if ($this->options->contains($option)) {
            $this->options->removeElement($option);
            // set the owning side to null (unless already changed)
            if ($option->getTracking() === $this) {
                $option->setTracking(null);
            }
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

        return $this;
    }
}
