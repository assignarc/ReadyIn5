<?php

namespace RI5\DB\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * Customer
 *
 * @ORM\Table(name="customer", uniqueConstraints={@ORM\UniqueConstraint(name="IX_Phone", columns={"phone"})})
 * @ORM\Entity(repositoryClass="RI5\DB\Repository\CustomerRepository")
 */
class Customer extends BaseEntity implements JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="userid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userid;

    

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone", type="string", length=22, nullable=true)
     */
    private $phone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="firstname", type="string", length=45, nullable=true)
     */
    private $firstname;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lastname", type="string", length=45, nullable=true)
     */
    private $lastname;

        /**
     * @var string|null
     *
     * @ORM\Column(name="addressline1", type="string", length=100, nullable=true)
     */
    private $addressline1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="addressline2", type="string", length=100, nullable=true)
     */
    private $addressline2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="addressline3", type="string", length=100, nullable=true)
     */
    private $addressline3;

    /**
     * @var string|null
     *
     * @ORM\Column(name="city", type="string", length=100, nullable=true)
     */
    private $city;

    /**
     * @var string|null
     *
     * @ORM\Column(name="state", type="string", length=100, nullable=true)
     */
    private $state;

    /**
     * @var string|null
     *
     * @ORM\Column(name="country", type="string", length=45, nullable=true)
     */
    private $country;

    /**
     * @var string|null
     *
     * @ORM\Column(name="postalCode", type="string", length=100, nullable=true)
     */
    private $postalcode;

    /**
     * @var string|"sms"
     *
     * @ORM\Column(name="contactMethod", type="string", length=10, nullable=false)
     */
    private $contactMethod;

     /**
     * One Customer has Many Reservations.
     * @ORM\JoinColumn(name="userid", referencedColumnName="userid")
     * @ORM\OneToMany(targetEntity="Reservation", mappedBy="customer") ]
     */
    private Collection $reservations;
    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }
    public function getUserid(): ?int
    {
        return $this->userid;
    }
    public function setUserId(?string $userid): self
    {
        $this->userid = $userid;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

  

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }
   
    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }


    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }


    public function getAddressline1(): ?string
    {
        return $this->addressline1;
    }

    public function setAddressline1(?string $addressline1): self
    {
        $this->addressline1 = $addressline1;

        return $this;
    }

    public function getAddressline2(): ?string
    {
        return $this->addressline2;
    }

    public function setAddressline2(?string $addressline2): self
    {
        $this->addressline2 = $addressline2;

        return $this;
    }

    public function getAddressline3(): ?string
    {
        return $this->addressline3;
    }

    public function setAddressline3(?string $addressline3): self
    {
        $this->addressline3 = $addressline3;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getPostalcode(): ?string
    {
        return $this->postalcode;
    }

    public function setPostalcode(?string $postalcode): self
    {
        $this->postalcode = $postalcode;

        return $this;
    }

    public function getReservations(): ?array
    {
        return $this->reservations->toArray();
    }

    public function setReservations(array $reservations): self
    {
        $this->postalcode = $reservations;
        return $this;
    }
    public function getContactMethod(): ?string
    {
        return $this->contactMethod;
    }
   
    public function setContactMethod(?string $contactMethod): self
    {
        $this->contactMethod = $contactMethod;

        return $this;
    }
    
    public function jsonSerialize() :mixed
    {
        return get_object_vars($this);
    }

}
