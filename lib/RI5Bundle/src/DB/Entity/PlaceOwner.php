<?php

namespace RI5\DB\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

 
 /**
 * PlaceOwner
 *
 * @ORM\Table(name="place_owner", uniqueConstraints={@ORM\UniqueConstraint(name="phone_UNIQUE", columns={"phone"})}, indexes={@ORM\Index(name="IX_Email", columns={"email"}), @ORM\Index(name="IX_Phone", columns={"phone"})})
 * @ORM\Entity(repositoryClass="RI5\DB\Repository\PlaceOwnerRepository")
 */
class PlaceOwner extends BaseEntity implements JsonSerializable 
{
    /** 
     * @var int
     *
     * @ORM\Column(name="ownerid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $ownerid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=true)
     */
    private $phone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=true)
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="addressline1", type="string", length=100, nullable=true)
     */
    private $addressline1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="addressline2", type="string", length=45, nullable=true)
     */
    private $addressline2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="addressline3", type="string", length=45, nullable=true)
     */
    private $addressline3;

    /**
     * @var string|null
     *
     * @ORM\Column(name="city", type="string", length=45, nullable=true)
     */
    private $city;

    /**
     * @var string|null
     *
     * @ORM\Column(name="state", type="string", length=45, nullable=true)
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
     * @ORM\Column(name="postalCode", type="string", length=10, nullable=true)
     */
    private $postalcode;

    /**
     * @var bool
     *
     * @ORM\Column(name="emailvalidated", type="boolean", nullable=false)
     */
    private $emailvalidated = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="phonevalidated", type="boolean", nullable=false)
     */
    private $phonevalidated = '0';

   
    /**
     * @var Collection
     * One Place has Many PlaceOwners.
     * @ORM\JoinColumn(name="ownerid", referencedColumnName="ownerid")
     * @ORM\OneToMany(targetEntity="Place", mappedBy="placeOwner") ]
     */
    private Collection $places;


    public function __construct()
    {
        $this->places= new ArrayCollection();
    }
    public function getOwnerid(): ?int
    {
        return $this->ownerid;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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

    public function isEmailvalidated(): ?bool
    {
        return $this->emailvalidated;
    }

    public function setEmailvalidated(bool $emailvalidated): self
    {
        $this->emailvalidated = $emailvalidated;

        return $this;
    }

    public function isPhonevalidated(): ?bool
    {
        return $this->phonevalidated;
    }

    public function setPhonevalidated(bool $phonevalidated): self
    {
        $this->phonevalidated = $phonevalidated;

        return $this;
    }

    
    public function getPlaces(): ?array
    {
        return $this->places->toArray();
    }

    public function setPlaces( $places): self
    {
        $this->places = new ArrayCollection($places);
        return $this;
    }
    public function jsonSerialize() : mixed
    {
        return get_object_vars($this);
    }

}
