<?php

namespace RI5\DB\Entity;

use Doctrine\ORM\Mapping as ORM;
use RI5\DB\Entity\Place;

/**
 * PlaceCapacity
 *
 * @ORM\Table(name="place_capacity", indexes={@ORM\Index(name="IDX_9FB356F7330FBA00", columns={"placeid"})})
 * @ORM\Entity
 */
class PlaceCapacity
{
    /**
     * @var int
     *
     * @ORM\Column(name="queueid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $queueid;

    /**
     * @var string
     *
     * @ORM\Column(name="key", type="string", length=25, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $key;

    /**
     * @var string|null
     *
     * @ORM\Column(name="value", type="string", length=100, nullable=true)
     */
    private $value;

   /**
     * Many PlaceHolidays has One Place.
     * @ORM\ManyToOne(targetEntity="Place")
     * @ORM\JoinColumn(name="placeid", referencedColumnName="placeid")
     */
    private Place $place;

    /**
     * @var int
     *
     * @ORM\Column(name="placeid", type="integer", nullable=false)
     */
    private $placeid;

    public function getQueueid(): ?int
    {
        return $this->queueid;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getPlace(): Place
    {   
        return $this->place;
    }

    public function setPlace(Place $place): self
    {   
        $this->place = $place;
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
}
