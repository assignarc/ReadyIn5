<?php

namespace RI5\DB\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * Reservation
 *
 * @ORM\Table(name="reservation", indexes={@ORM\Index(name="IX_Queue", columns={"queueid"}), @ORM\Index(name="IX_Place", columns={"placeid"}), @ORM\Index(name="IX_User", columns={"userid"})})
 * @ORM\Entity(repositoryClass="RI5\DB\Repository\ReservationRepository")
 */
class Reservation extends BaseEntity implements JsonSerializable 
{
    /**
     * @var int
     *
     * @ORM\Column(name="reservationid", type="integer", nullable=false)
     * @ORM\Id
     */
    private $reservationid;

    /**
     * @var int
     *
     * @ORM\Column(name="userid", type="integer", nullable=false)
     */
    private $userid;

    /**
     * @var int
     *
     * @ORM\Column(name="placeid", type="integer", nullable=false)
     */
    private $placeid;

    /**
     * @var int
     *
     * @ORM\Column(name="queueid", type="integer", nullable=false)
     */
    private $queueid;

    /**
     * @var int
     *
     * @ORM\Column(name="adults", type="integer", nullable=false)
     */
    private $adults;

    /**
     * @var int|null
     *
     * @ORM\Column(name="children", type="integer", nullable=true)
     */
    private $children;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="reservation_dt", type="datetime", nullable=false)
     */
    private $reservationDt;

    /**
     * @var null
     *
     * @ORM\Column(name="notes", type="json", nullable=true)
     */
    private $notes;
    /**
     * @var string|null
     *
     * @ORM\Column(name="status", type="string",length=6, nullable=false)
     */
    private $status;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="status_dt", type="datetime", nullable=true)
     */
    private $statusDt;

      /**
     * @var string|null
     *
     * @ORM\Column(name="instructions", type="string",length=500, nullable=true)
     */
    private $instructions;

    /**
     * Many Reservations has One Customer.
     * @ORM\ManyToOne(targetEntity="Customer", inversedBy="reservations")
     * @ORM\JoinColumn(name="userid", referencedColumnName="userid")
     */
    private Customer $customer;

  
    /**
     * Many Reservations has One Place.
     * @ORM\ManyToOne(targetEntity="Place", inversedBy="reservations" , cascade={"persist","refresh"})
     * @ORM\JoinColumn(name="placeid", referencedColumnName="placeid")
     */
    private Place $place;



    public function getCustomer(): Customer
    {
        return $this->customer;
    }
    
    public function setCustomer(Customer $customer): self
    {   
        $this->customer = $customer;
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
   
    public function getReservationid(): ?int
    {
        return $this->reservationid;
    }
    public function setReservationid(int $reservationid): self
    {
         $this->reservationid = $reservationid;
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

    public function getUserid(): ?int
    {
        return $this->userid;
    }

    public function setUserid(int $userid): self
    {
        $this->userid = $userid;

        return $this;
    }

    public function getQueueid(): ?int
    {
        return $this->queueid;
    }

    public function setQueueid(int $queueid): self
    {
        $this->queueid = $queueid;

        return $this;
    }

    public function getAdults(): ?int
    {
        return $this->adults;
    }

    public function setAdults(int $adults): self
    {
        $this->adults = $adults;

        return $this;
    }

    public function getChildren(): ?int
    {
        return $this->children;
    }

    public function setChildren(?int $children): self
    {
        $this->children = $children;

        return $this;
    }

    public function getReservationDt(): ?\DateTimeInterface
    {
        return $this->reservationDt;
    }

    public function setReservationDt(\DateTimeInterface $reservationDt): self
    {
        $this->reservationDt = $reservationDt;

        return $this;
    }

    public function getNotes() : Mixed
    {
        return $this->notes;
    }
   
    public function setNotes($notes): self
    {
        $this->notes = $notes;

        return $this;
    }
    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }
    public function getInstructions(): ?string
    {
        return $this->instructions;
    }

    public function setInstructions(?string $instructions): self
    {
        $this->instructions = $instructions;

        return $this;
    }
    public function getStatusDt(): ?\DateTimeInterface
    {
        return $this->statusDt;
    }

    public function setStatusDt(?\DateTimeInterface $statusDt): self
    {
        $this->statusDt = $statusDt;

        return $this;
    }
    public function jsonSerialize() : mixed
    {
        return get_object_vars($this);
    }

}
