<?php

namespace RI5\DB\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use RI5\DB\Entity\Place;
/**
 * PlaceHolidays
 *
 * @ORM\Table(name="place_holidays", indexes={@ORM\Index(name="IX_Place_Holiday", columns={"placeid", "holiday_date"}), @ORM\Index(name="IDX_103D468FA68B9EA2", columns={"holidayid"})})
 * @ORM\Entity
 */
class PlaceHolidays extends BaseEntity implements JsonSerializable 
{
  
    /**
     * @var int
     *
     * @ORM\Column(name="holidayId", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $holidayid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="holiday_date", type="date", nullable=false)
     */
    private $holidayDate;

    /**
     * @var string
     *
     * @ORM\Column(name="holiday_name", type="string", length=45, nullable=false)
     */
    private $holidayName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="special_note", type="string", length=100, nullable=true)
     */
    private $specialNote;

  
   

    /**
     * Many PlaceHolidays has One Place.
     * @ORM\ManyToOne(targetEntity="Place", inversedBy="placeHolidays" , cascade={"persist","refresh"})
     * @ORM\JoinColumn(name="placeid", referencedColumnName="placeid")
    */
    private Place $place;


    /**
     * @var int
     *
     * @ORM\Column(name="placeid", type="integer", nullable=false)
     */
    private $placeid;

    public function getHolidayid(): ?int
    {
        return $this->holidayid;
    }

    public function getHolidayDate(): ?\DateTimeInterface
    {
        return $this->holidayDate;
    }

    public function setHolidayDate(\DateTimeInterface $holidayDate): self
    {
        $this->holidayDate = $holidayDate;

        return $this;
    }

    public function getHolidayName(): ?string
    {
        return $this->holidayName;
    }

    public function setHolidayName(string $holidayName): self
    {
        $this->holidayName = $holidayName;

        return $this;
    }

    public function getSpecialNote(): ?string
    {
        return $this->specialNote;
    }

    public function setSpecialNote(?string $specialNote): self
    {
        $this->specialNote = $specialNote;

        return $this;
    }

    
    public function getPlace(): Place
    {
        return $this->place;
    }

    public function setPlace(Place $place): self
    {
        $this->place = $place;
        if($place->getPlaceid())
            $this->setPlaceid($place->getPlaceid());

        return $this;
    }
   
   
    public function getPlaceid(): int
    {
        return $this->placeid;
    }

    public function setPlaceid(int $placeid): self
    {
        $this->placeid = $placeid;
        return $this;
    }
    public function jsonSerialize() : mixed
    {
        return get_object_vars($this);
    }
}
