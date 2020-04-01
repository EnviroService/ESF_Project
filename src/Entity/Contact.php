<?php


namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Contact
{
    /**
     * @var string|null
     * @Assert\NotBlank()
     *
     */
    private $society;

    /**
     * @var string|null
     * @Asser\NotBlank()
     */
    private $firstname;

    /**
     * @var string|null
     * @Assert\NotBlank()
     */
    private $lastname;

    /**
     * @var string|null
     */

    private $siret;

    /**
     * @var string|null
     * @Assert\NotBlank()
     * @Assert\Email()
     */

    private $mail;

    /**
     * @var string|null
     * @Assert\NotBlank()
     */

    private $num_tva;

    /**
     * @var string|null
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/[0-9]{10}/"
     * )
     */

    private $phone_number;

    /**
     * @var string|null
     * @Assert\NotBlank()
     * @Assert\Length(min="10")
     */

    private $message;

    /**
     * @var string|null
     * @Assert\Length(min="10")
     */

    private $kbis;

    /**
     * @var string|null
     * @Assert\Length(min="10")
     */

    private $cni;

    /**
     * @return string|null
     */
    public function getSociety(): ?string
    {
        return $this->society;
    }

    /**
     * @param string|null $society
     * @return Contact
     */
    public function setSociety(?string $society): Contact
    {
        $this->society = $society;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     * @return Contact
     */
    public function setFirstname(?string $firstname): Contact
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     * @return Contact
     */
    public function setLastname(?string $lastname): Contact
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSiret(): string
    {
        return $this->siret;
    }

    /**
     * @param string|null $siret
     * @return Contact
     */
    public function setSiret(string $siret): Contact
    {
        $this->siret = $siret;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMail(): string
    {
        return $this->mail;
    }

    /**
     * @param string|null $mail
     * @return Contact
     */
    public function setMail(string $mail): Contact
    {
        $this->mail = $mail;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNumTva(): string
    {
        return $this->num_tva;
    }

    /**
     * @param string|null $num_tva
     * @return Contact
     */
    public function setNumTva(string $num_tva): Contact
    {
        $this->num_tva = $num_tva;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): string
    {
        return $this->phone_number;
    }

    /**
     * @param string|null $phone_number
     * @return Contact
     */
    public function setPhoneNumber(string $phone_number): Contact
    {
        $this->phone_number = $phone_number;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string|null $message
     * @return Contact
     */
    public function setMessage(string $message): Contact
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getKbis(): string
    {
        return $this->kbis;
    }

    /**
     * @param string|null $kbis
     * @return Contact
     */
    public function setKbis(string $kbis): Contact
    {
        $this->kbis = $kbis;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCni(): string
    {
        return $this->cni;
    }

    /**
     * @param string|null $cni
     * @return Contact
     */
    public function setCni(string $cni): Contact
    {
        $this->cni = $cni;
        return $this;
    }





}
