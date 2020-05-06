<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateBooking;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isReceived;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $receivedDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Tracking", mappedBy="booking")
     */
    private $trackings;

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
    private $isSentUser;

    /**
     * @ORM\Column(type="datetime")
     */
    private $isSentUserDate;

    public function __construct()
    {
        $this->trackings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateBooking(): ?\DateTimeInterface
    {
        return $this->dateBooking;
    }

    public function setDateBooking(\DateTimeInterface $dateBooking): self
    {
        $this->dateBooking = $dateBooking;

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
     * @return Collection|Tracking[]
     */
    public function getTrackings(): Collection
    {
        return $this->trackings;
    }

    public function addTracking(Tracking $tracking): self
    {
        if (!$this->trackings->contains($tracking)) {
            $this->trackings[] = $tracking;
            $tracking->setBooking($this);
        }

        return $this;
    }

    public function removeTracking(Tracking $tracking): self
    {
        if ($this->trackings->contains($tracking)) {
            $this->trackings->removeElement($tracking);
            // set the owning side to null (unless already changed)
            if ($tracking->getBooking() === $this) {
                $tracking->setBooking(null);
            }
        }

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

    public function getIsSentUser(): ?bool
    {
        return $this->isSentUser;
    }

    public function setIsSentUser(bool $isSentUser): self
    {
        $this->isSentUser = $isSentUser;

        return $this;
    }

    public function getIsSentUserDate(): ?\DateTimeInterface
    {
        return $this->isSentUserDate;
    }

    public function setIsSentUserDate(\DateTimeInterface $isSentUserDate): self
    {
        $this->isSentUserDate = $isSentUserDate;

        return $this;
    }

}
