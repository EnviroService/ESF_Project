<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank(message="Ne peut pas etre vide")
     * @Assert\Length(max="30", maxMessage="La valeur saisie {{ value }} est trop longue, elle ne devrait pas dépasser {{ limit }} caractères")
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
    */
    private $password;

    /**
     * @ORM\Column(type="string", length=40)
     * @Assert\NotBlank(message="Ne peut pas etre vide")
     * @Assert\Length(max="40", maxMessage="La valeur saisie {{ value }} est trop longue, elle ne devrait pas dépasser {{ limit }} caractères")
     */
    private $refSign;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Ne peut pas etre vide")
     * @Assert\Length(max="14", maxMessage="La valeur saisie {{ value }} est trop longue, elle ne devrait pas dépasser {{ limit }} caractères")
     */
    private $SIRET;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Ne peut pas etre vide, doit comporter FR + 11 chiffres")
     * @Assert\Length(max="13", maxMessage="La valeur saisie {{ value }} est trop longue, elle ne devrait pas dépasser {{ limit }} caractères")
     */
    private $numTVA;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Regex(pattern="/^\[a-z0-9._-]+@[a-z0-9_.-]+\\.[a-z]{2,4}$/",
     *     match=false,
     *     message="Format d'email non reconnu")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=55)
     * @Assert\NotBlank(message="Ne peut pas etre vide")
     * @Assert\Length(max="55", maxMessage="La valeur saisie {{ value }} est trop longue, elle ne devrait pas dépasser {{ limit }} caractères")
     */
    private $billingAddress;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank(message="Ne peut pas etre vide")
     * @Assert\Length(max="30", maxMessage="La valeur saisie {{ value }} est trop longue, elle ne devrait pas dépasser {{ limit }} caractères")
     */
    private $billingCity;

    /**
     * @ORM\Column(type="string", length=10)
     * @Assert\NotBlank(message="Ne peut pas etre vide")
     * @Assert\Length(max="10", maxMessage="La valeur saisie {{ value }} est trop longue, elle ne devrait pas dépasser {{ limit }} caractères")
     */
    private $billingPostcode;

    /**
     * @ORM\Column(type="boolean")
     */
    private $justifyDoc;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank(message="Ne peut pas etre vide")
     * @Assert\Length(max="30", maxMessage="La valeur saisie {{ value }} est trop longue, elle ne devrait pas dépasser {{ limit }} caractères")
     */
    private $operationalAddress;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank(message="Ne peut pas etre vide")
     * @Assert\Length(max="30", maxMessage="La valeur saisie {{ value }} est trop longue, elle ne devrait pas dépasser {{ limit }} caractères")
     */
    private $operationalCity;

    /**
     * @ORM\Column(type="string", length=10)
     * @Assert\NotBlank(message="Ne peut pas etre vide")
     * @Assert\Length(max="10", maxMessage="La valeur saisie {{ value }} est trop longue, elle ne devrait pas dépasser {{ limit }} caractères")
     */
    private $operationalPostcode;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank(message="Ne peut pas etre vide")
     * @Assert\Length(max="30", maxMessage="La valeur saisie {{ value }} est trop longue, elle ne devrait pas dépasser {{ limit }} caractères")
     */
    private $refContact;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank(message="Ne peut pas etre vide")
     * @Assert\Length(max="30", maxMessage="La valeur saisie {{ value }} est trop longue, elle ne devrait pas dépasser {{ limit }} caractères")

     */
    private $bossName;

    /**
     * @ORM\Column(type="datetime")
     */
    private $signinDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $signupDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $erpClient;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $kbis;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cni;

    /**
     * @ORM\Column(type="float")
     */
    private $bonusRateCard;

    /**
     * @ORM\Column(type="float")
     */
    private $bonusOption;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Devis", mappedBy="user", orphanRemoval=true)
     */
    private $devis;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Factures", mappedBy="user", orphanRemoval=true)
     */
    private $factures;

    public function __construct()
    {
        $this->devis = new ArrayCollection();
        $this->factures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getRefSign(): ?string
    {
        return $this->refSign;
    }

    public function setRefSign(string $refSign): self
    {
        $this->refSign = $refSign;

        return $this;
    }

    public function getSIRET(): ?int
    {
        return $this->SIRET;
    }

    public function setSIRET(int $SIRET): self
    {
        $this->SIRET = $SIRET;

        return $this;
    }

    public function getNumTVA(): ?string
    {
        return $this->numTVA;
    }

    public function setNumTVA(string $numTVA): self
    {
        $this->numTVA = $numTVA;

        return $this;
    }


    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }


    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }
    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }
    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }
    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }


    public function getBillingAddress(): ?string
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(string $billingAddress): self
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    public function getBillingCity(): ?string
    {
        return $this->billingCity;
    }

    public function setBillingCity(string $billingCity): self
    {
        $this->billingCity = $billingCity;

        return $this;
    }

    public function getBillingPostcode(): ?string
    {
        return $this->billingPostcode;
    }

    public function setBillingPostcode(string $billingPostcode): self
    {
        $this->billingPostcode = $billingPostcode;

        return $this;
    }

    public function getJustifyDoc(): ?bool
    {
        return $this->justifyDoc;
    }

    public function setJustifyDoc(bool $justifyDoc): self
    {
        $this->justifyDoc = $justifyDoc;

        return $this;
    }

    public function getOperationalAddress(): ?string
    {
        return $this->operationalAddress;
    }

    public function setOperationalAddress(string $operationalAddress): self
    {
        $this->operationalAddress = $operationalAddress;

        return $this;
    }

    public function getOperationalCity(): ?string
    {
        return $this->operationalCity;
    }

    public function setOperationalCity(string $operationalCity): self
    {
        $this->operationalCity = $operationalCity;

        return $this;
    }

    public function getOperationalPostcode(): ?string
    {
        return $this->operationalPostcode;
    }

    public function setOperationalPostcode(string $operationalPostcode): self
    {
        $this->operationalPostcode = $operationalPostcode;

        return $this;
    }

    public function getRefContact(): ?string
    {
        return $this->refContact;
    }

    public function setRefContact(string $refContact): self
    {
        $this->refContact = $refContact;

        return $this;
    }

    public function getBossName(): ?string
    {
        return $this->bossName;
    }

    public function setBossName(string $bossName): self
    {
        $this->bossName = $bossName;

        return $this;
    }

    public function getSigninDate(): ?\DateTimeInterface
    {
        return $this->signinDate;
    }

    public function setSigninDate(\DateTimeInterface $signinDate): self
    {
        $this->signinDate = $signinDate;

        return $this;
    }

    public function getSignupDate(): ?\DateTimeInterface
    {
        return $this->signupDate;
    }

    public function setSignupDate(\DateTimeInterface $signupDate): self
    {
        $this->signupDate = $signupDate;

        return $this;
    }

    public function getErpClient(): ?string
    {
        return $this->erpClient;
    }

    public function setErpClient(string $erpClient): self
    {
        $this->erpClient = $erpClient;

        return $this;
    }

    public function getKbis(): ?string
    {
        return $this->kbis;
    }

    public function setKbis(string $kbis): self
    {
        $this->kbis = $kbis;

        return $this;
    }

    public function getCni(): ?string
    {
        return $this->cni;
    }

    public function setCni(string $cni): self
    {
        $this->cni = $cni;

        return $this;
    }

    public function getBonusRateCard(): ?float
    {
        return $this->bonusRateCard;
    }

    public function setBonusRateCard(float $bonusRateCard): self
    {
        $this->bonusRateCard = $bonusRateCard;

        return $this;
    }

    public function getBonusOption(): ?float
    {
        return $this->bonusOption;
    }

    public function setBonusOption(float $bonusOption): self
    {
        $this->bonusOption = $bonusOption;

        return $this;
    }

    /**
     * @return Collection|Devis[]
     */
    public function getDevis(): Collection
    {
        return $this->devis;
    }

    public function addDevi(Devis $devi): self
    {
        if (!$this->devis->contains($devi)) {
            $this->devis[] = $devi;
            $devi->setUser($this);
        }

        return $this;
    }

    public function removeDevi(Devis $devi): self
    {
        if ($this->devis->contains($devi)) {
            $this->devis->removeElement($devi);
            // set the owning side to null (unless already changed)
            if ($devi->getUser() === $this) {
                $devi->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Factures[]
     */
    public function getFactures(): Collection
    {
        return $this->factures;
    }

    public function addFacture(Factures $facture): self
    {
        if (!$this->factures->contains($facture)) {
            $this->factures[] = $facture;
            $facture->setUser($this);
        }

        return $this;
    }

    public function removeFacture(Factures $facture): self
    {
        if ($this->factures->contains($facture)) {
            $this->factures->removeElement($facture);
            // set the owning side to null (unless already changed)
            if ($facture->getUser() === $this) {
                $facture->setUser(null);
            }
        }

        return $this;
    }
}