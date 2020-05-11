<?php


namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class EditContact
{
    /**
     * @var string|null
     * @Assert\NotBlank
     * @Assert\Length(min=2, max=100)
     */
    private $username;

    /**
     * @var string|null
     * @Assert\NotBlank
     * @Assert\Length(min=2, max=100)
     */
    private $bossname;

    /**
     * @var string|null
     * @Assert\NotBlank
     * @Assert\Regex(
     *  pattern = "/[0-9]{10}/"
     * )
     */
    private $numPhone;

    /**
     * @var string|null
     * @Assert\NotBlank
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string|null
     * @Assert\NotBlank
     */
    private $subject;

    /**
     * @var string|null
     * @Assert\NotBlank
     * @Assert\Length(min=10)
     */
    private $message;

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     * @return EditContact
     */
    public function setUsername(?string $username): EditContact
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBossname(): ?string
    {
        return $this->bossname;
    }

    /**
     * @param string|null $bossname
     * @return EditContact
     */
    public function setBossname(?string $bossname): EditContact
    {
        $this->bossname = $bossname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNumPhone(): ?string
    {
        return $this->numPhone;
    }

    /**
     * @param string|null $numPhone
     * @return EditContact
     */
    public function setNumPhone(?string $numPhone): EditContact
    {
        $this->numPhone = $numPhone;
        return $this;
    }


    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return EditContact
     */
    public function setEmail(?string $email): EditContact
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * @param string|null $subject
     * @return EditContact
     */
    public function setSubject(?string $subject): EditContact
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string|null $message
     * @return EditContact
     */
    public function setMessage(?string $message): EditContact
    {
        $this->message = $message;
        return $this;
    }


}
