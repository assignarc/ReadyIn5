<?php

namespace RI5\DB\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * PlaceQueue
 *
 * @ORM\Table(name="place_queue", uniqueConstraints={@ORM\UniqueConstraint(name="IX_QueueName", columns={"queuename", "placeid"})}, indexes={@ORM\Index(name="IX_Place_queue", columns={"placeid"})})
 * @ORM\Entity(repositoryClass="RI5\DB\Repository\PlaceQueueRepository")
 */
class PlaceQueue extends BaseEntity implements JsonSerializable 
{
    /**
     * @var int
     *
     * @ORM\Column(name="queueid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $queueid;

    /**
     * @var string
     *
     * @ORM\Column(name="queuename", type="string", length=45, nullable=false)
     */
    private $queuename;

    /**
     * @var int|null
     *
     * @ORM\Column(name="capacity_adults", type="integer", nullable=true)
     */
    private $capacityAdults;

    /**
     * @var int|null
     *
     * @ORM\Column(name="capacity_children", type="integer", nullable=true)
     */
    private $capacityChildren;

    /**
     * @var int
     *
     * @ORM\Column(name="capcity_total", type="integer", nullable=false)
     */
    private $capcityTotal;

    
    /**
     * @var int
     *
     * @ORM\Column(name="placeid", type="integer", nullable=false)
     */
    private $placeid;

    /**
     * Many PlaceQueues have One Place.
     * @ORM\ManyToOne(targetEntity="Place", inversedBy="placeQueues")
     * @ORM\JoinColumn(name="placeid", referencedColumnName="placeid")
     */
    private Place $place;

    public function getQueueid(): ?int
    {
        return $this->queueid;
    }

    public function getQueuename(): ?string
    {
        return $this->queuename;
    }

    public function setQueuename(string $queuename): self
    {
        $this->queuename = $queuename;

        return $this;
    }

    public function getCapacityAdults(): ?int
    {
        return $this->capacityAdults;
    }

    public function setCapacityAdults(?int $capacityAdults): self
    {
        $this->capacityAdults = $capacityAdults;

        return $this;
    }

    public function getCapacityChildren(): ?int
    {
        return $this->capacityChildren;
    }

    public function setCapacityChildren(?int $capacityChildren): self
    {
        $this->capacityChildren = $capacityChildren;

        return $this;
    }

    public function getCapcityTotal(): ?int
    {
        return $this->capcityTotal;
    }

    public function setCapcityTotal(int $capcityTotal): self
    {
        $this->capcityTotal = $capcityTotal;

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

    public function jsonSerialize() : mixed
    {
        return get_object_vars($this);
    }
}
