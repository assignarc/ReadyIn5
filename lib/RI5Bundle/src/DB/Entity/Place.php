<?php

namespace RI5\DB\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JsonSerializable;
use RI5\DB\Entity\PlaceOwner;

/**
 * Place
 *
 * @ORM\Table(name="place", uniqueConstraints={@ORM\UniqueConstraint(name="IX_Slug", columns={"slug"})}, indexes={@ORM\Index(name="IX_State", columns={"state"}), @ORM\Index(name="IX_Owner", columns={"ownerid"}), @ORM\Index(name="IX_country", columns={"country"}), @ORM\Index(name="IX_City", columns={"city"}), @ORM\Index(name="IX_PostalCode", columns={"postalCode"})})
 * @ORM\Entity(repositoryClass="RI5\DB\Repository\PlaceRepository")
 */
class Place extends BaseEntity implements JsonSerializable 
{
    /**
     * @var int
     *
     * @ORM\Column(name="placeid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $placeid;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=400, nullable=false)
     */
    private $slug;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=200, nullable=true)
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
     * @ORM\Column(name="addressline4", type="string", length=100, nullable=true)
     */
    private $addressline4;

    /**
     * @var string|null
     *
     * @ORM\Column(name="addressline5", type="string", length=100, nullable=true)
     */
    private $addressline5;
    
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
     * @ORM\Column(name="country", type="string", length=100, nullable=true)
     */
    private $country;
    /**
     * @var string|null
     *
     * @ORM\Column(name="postalCode", type="string", length=100, nullable=true)
     */
    private $postalcode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=true)
     */
    private $phone;

    /**
     * @var null
     *
     * @ORM\Column(name="addressdata", type="json", nullable=true)
     */
    private $addressdata;

    /**
     * One Place has Many Holidays.
     * @ORM\JoinColumn(name="placeid", referencedColumnName="placeid")
     * @ORM\OneToMany(targetEntity="PlaceHolidays", mappedBy="place") 
     */
    public Collection $placeHolidays;

    /**
     * One Place has Many PlaceQueues.
     * @ORM\JoinColumn(name="placeid", referencedColumnName="placeid")
     * @ORM\OneToMany(targetEntity="PlaceQueue", mappedBy="place") 
     */
    private Collection $placeQueues;

    /**
     * One Place has Many PlaceSchedules.
     * @ORM\JoinColumn(name="placeid", referencedColumnName="placeid")
     * @ORM\OneToMany(targetEntity="PlaceSchedule", mappedBy="place") 
     */
    private Collection $placeSchedules;
    
    /**
     * One Place has Many PlaceUsers.
     * @ORM\JoinColumn(name="placeid", referencedColumnName="placeid")
     * @ORM\OneToMany(targetEntity="PlaceUser", mappedBy="place") 
     */
    private Collection $placeUsers;

    /**
     * One Place has Many Reservations.
     * @ORM\JoinColumn(name="placeid", referencedColumnName="placeid")
     * @ORM\OneToMany(targetEntity="Reservation", mappedBy="place") 
     */
    private Collection $reservations;

    /**
     * One Place has Many PlaceImages.
     * @ORM\JoinColumn(name="placeid", referencedColumnName="placeid")
     * @ORM\OneToMany(targetEntity="PlaceImages", mappedBy="place") 
     */
    private Collection $placeImages;

     /**
     * One Place has Many Preferences.
     * @ORM\JoinColumn(name="placeid", referencedColumnName="placeid")
     * @ORM\OneToMany(targetEntity="PlacePrefs", mappedBy="place") 
     */
    private Collection $placePrefs;

  
     /**
     * @var PlaceOwner
     *
     * @ORM\ManyToOne(targetEntity="PlaceOwner", inversedBy="places",cascade={"refresh"})
     * @ORM\JoinColumn(name="ownerid", referencedColumnName="ownerid")
     * 
     */
    private PlaceOwner $placeOwner;

     /**
     * @var int|null
     *
     * @ORM\ManyToOne(targetEntity="PlaceOwner")
     * @ORM\JoinColumns(name="ownerid", referencedColumnName="ownerid")
     * 
     */
    private $ownerid;

    public mixed $data;

    public function __construct()
    {
        $this->placeHolidays = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->placeQueues= new ArrayCollection();
        $this->placeSchedules= new ArrayCollection();
        $this->placeUsers= new ArrayCollection();
        $this->placePrefs = new ArrayCollection();
       
        $this->data= [];
    }

    public function getData():mixed{
        return $this->data;
    }
    public function getPlaceid(): ?int
    {
        return $this->placeid;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    public function getAddressline4(): ?string
    {
        return $this->addressline4;
    }

    public function setAddressline4(?string $addressline4): self
    {
        $this->addressline4 = $addressline4;

        return $this;
    }

    public function getAddressline5(): ?string
    {
        return $this->addressline5;
    }

    public function setAddressline5(?string $addressline5): self
    {
        $this->addressline5 = $addressline5;

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
   
    public function setPlaceId(int $placeid): self
    {
        $this->placeid = $placeid;
        return $this;
    }

    public function getPlaceHolidays(): ?array
    {
        return $this->placeHolidays->toArray();
    }

    public function setPlaceQueues(array $placeQueues): self
    {
        $this->placeQueues = new ArrayCollection($placeQueues);
        return $this;
    }

    public function getPlaceQueues(): array
    {
        return $this->placeQueues->toArray();
    }

    public function setPlaceSchedules(array $placeSchedules): self
    {
        $this->placeSchedules = new ArrayCollection($placeSchedules);
        return $this;
    }

    public function getPlaceSchedules(): array
    {
        return $this->placeSchedules->toArray();
    }
    public function setPlacePrefs(array $placePrefs): self
    {
        $this->placePrefs = new ArrayCollection($placePrefs);
        return $this;
    }

    public function getPlacePrefs(): array
    {
        return $this->placePrefs->toArray();
    }

    public function setPlaceUsers(array $placeUsers): self
    {
        $this->placeUsers = new ArrayCollection($placeUsers);
        return $this;
    }

    public function getPlaceUsers(): array
    {
        return $this->placeUsers->toArray();
    }

    public function setPlaceImages(array $placeImages): self
    {
        $this->placeImages = new ArrayCollection($placeImages);
        return $this;
    }

    public function getPlaceImages(): array
    {
        return $this->placeImages->toArray();
    }

    public function setReservations(array $reservations): self
    {
        $this->reservations = new ArrayCollection($reservations);
        return $this;
    }

    public function getReservations(): array
    {
        return $this->reservations->toArray();
    }

    public function setPlaceHolidays(array $placeHolidays): self
    {
        $this->placeHolidays = new ArrayCollection($placeHolidays);
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddressdata()
    {
        return $this->addressdata;
    }

    public function setAddressdata($addressdata): self
    {
        $this->addressdata = $addressdata;

        return $this;
    }
    public function getOwnerId() :?int
    {
        return $this->ownerid;
    }

    public function setOwnerId(int $ownerid): self
    {
        $this->ownerid =$ownerid;
        return $this;
    }
    public function getPlaceOwner(): PlaceOwner 
    {
        return $this->placeOwner;
    }

    public function setPlaceOwner(PlaceOwner $placeOwner): self
    {
        $this->placeOwner =$placeOwner;
        return $this;
    }

    public function jsonSerialize() : mixed
    {
        return get_object_vars($this);
    }

}
