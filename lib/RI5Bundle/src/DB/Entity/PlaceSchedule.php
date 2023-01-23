<?php

namespace RI5\DB\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * PlaceSchedule
 *
 * @ORM\Table(name="place_schedule", indexes={@ORM\Index(name="IX_Place", columns={"placeid"}), @ORM\Index(name="IX_PlaceOpenClose", columns={"placeid", "day", "shift"})})
 * @ORM\Entity(repositoryClass="RI5\DB\Repository\PlaceScheduleRepository")
 */
class PlaceSchedule extends BaseEntity implements JsonSerializable 
{
    /**
     * @var int
     *
     * @ORM\Column(name="scheduleid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $scheduleid;

    /**
     * @var string
     *
     * @ORM\Column(name="day", type="string", length=9, nullable=false)
     */
    private $day;

    /**
     * @var string
     *
     * @ORM\Column(name="open_time",  type="string", length=9,nullable=false)
     */
    private $openTime;

    /**
     * @var string
     *
     * @ORM\Column(name="close_time", type="string", length=9, nullable=false)
     */
    private $closeTime;

    /**
     * @var string
     *
     * @ORM\Column(name="shift", type="string", length=10, nullable=false)
     */
    private $shift;

   
    /**
     * @var int
     *
     * @ORM\Column(name="placeid", type="integer", nullable=false)
     */
    private $placeid;
  

    public function getScheduleid(): ?int
    {
        return $this->scheduleid;
    }

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function setDay(string $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getOpenTime(): ?string
    {
        return $this->openTime;
    }

    public function setOpenTime(string $openTime): self
    {
        $this->openTime = $openTime;

        return $this;
    }

    public function getCloseTime(): ?string
    {
        return $this->closeTime;
    }

    public function setCloseTime(string $closeTime): self
    {
        $this->closeTime = $closeTime;

        return $this;
    }

    public function getShift(): ?string
    {
        return $this->shift;
    }

    public function setShift(string $shift): self
    {
        $this->shift = $shift;

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
