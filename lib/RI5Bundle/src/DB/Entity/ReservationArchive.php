<?php

namespace RI5\DB\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * ReservationArchive
 *
 * @ORM\Table(name="reservation_archive", indexes={@ORM\Index(name="IX_RA_DT", columns={"reservation_dt"}), @ORM\Index(name="IX_RA_Queue", columns={"queueid"}), @ORM\Index(name="IX_RA_ReservationId", columns={"reservationid"})})
 * @ORM\Entity
 */
class ReservationArchive extends BaseEntity implements JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="reservationarchiveid", type="bigint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $reservationarchiveid;

    /**
     * @var int
     *
     * @ORM\Column(name="reservationid", type="bigint", nullable=false, options={"unsigned"=true})
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
     * @var \DateTime|null
     *
     * @ORM\Column(name="status_wait_dt", type="datetime", nullable=true)
     */
    private $statusWaitDt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="status_call_dt", type="datetime", nullable=true)
     */
    private $statusCallDt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="status_seat_dt", type="datetime", nullable=true)
     */
    private $statusSeatDt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="status_serve_dt", type="datetime", nullable=true)
     */
    private $statusServeDt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="status_complete_dt", type="datetime", nullable=true)
     */
    private $statusCompleteDt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="status_cancel_dt", type="datetime", nullable=true)
     */
    private $statusCancelDt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="status_noshow_dt", type="datetime", nullable=true)
     */
    private $statusNoshowDt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="status_expired_dt", type="datetime", nullable=true)
     */
    private $statusExpiredDt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="g_year", type="string", length=4, nullable=true)
     */
    private $gYear;

    /**
     * @var int|null
     *
     * @ORM\Column(name="g_month", type="integer", nullable=true)
     */
    private $gMonth;

    /**
     * @var int|null
     *
     * @ORM\Column(name="g_weekday", type="integer", nullable=true)
     */
    private $gWeekday;

    /**
     * @var string|null
     *
     * @ORM\Column(name="g_dayofyear", type="string", length=3, nullable=true)
     */
    private $gDayofyear;

    /**
     * @var int|null
     *
     * @ORM\Column(name="g_wait", type="integer", nullable=true)
     */
    private $gWait;

    /**
     * @var int|null
     *
     * @ORM\Column(name="g_seat", type="integer", nullable=true)
     */
    private $gSeat;

    /**
     * @var int|null
     *
     * @ORM\Column(name="g_serve", type="integer", nullable=true)
     */
    private $gServe;

    /**
     * @var int|null
     *
     * @ORM\Column(name="g_complete", type="integer", nullable=true)
     */
    private $gComplete;

    /**
     * @var int|null
     *
     * @ORM\Column(name="g_cancel", type="integer", nullable=true)
     */
    private $gCancel;

    /**
     * @var int|null
     *
     * @ORM\Column(name="g_noshow", type="integer", nullable=true)
     */
    private $gNoshow;

    /**
     * @var int|null
     *
     * @ORM\Column(name="g_expired", type="integer", nullable=true)
     */
    private $gExpired;

    /**
     * @var string|null
     *
     * @ORM\Column(name="instructions", type="string", length=500, nullable=true)
     */
    private $instructions;

    /**
     * @var array|null
     *
     * @ORM\Column(name="notes", type="json", nullable=true)
     */
    private $notes;

    public function getReservationarchiveid(): ?string
    {
        return $this->reservationarchiveid;
    }

    public function getReservationid(): ?string
    {
        return $this->reservationid;
    }

    public function setReservationid(string $reservationid): self
    {
        $this->reservationid = $reservationid;

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

    public function getPlaceid(): ?int
    {
        return $this->placeid;
    }

    public function setPlaceid(int $placeid): self
    {
        $this->placeid = $placeid;

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

    public function getStatusWaitDt(): ?\DateTimeInterface
    {
        return $this->statusWaitDt;
    }

    public function setStatusWaitDt(?\DateTimeInterface $statusWaitDt): self
    {
        $this->statusWaitDt = $statusWaitDt;

        return $this;
    }

    public function getStatusCallDt(): ?\DateTimeInterface
    {
        return $this->statusCallDt;
    }

    public function setStatusCallDt(?\DateTimeInterface $statusCallDt): self
    {
        $this->statusCallDt = $statusCallDt;

        return $this;
    }

    public function getStatusSeatDt(): ?\DateTimeInterface
    {
        return $this->statusSeatDt;
    }

    public function setStatusSeatDt(?\DateTimeInterface $statusSeatDt): self
    {
        $this->statusSeatDt = $statusSeatDt;

        return $this;
    }

    public function getStatusServeDt(): ?\DateTimeInterface
    {
        return $this->statusServeDt;
    }

    public function setStatusServeDt(?\DateTimeInterface $statusServeDt): self
    {
        $this->statusServeDt = $statusServeDt;

        return $this;
    }

    public function getStatusCompleteDt(): ?\DateTimeInterface
    {
        return $this->statusCompleteDt;
    }

    public function setStatusCompleteDt(?\DateTimeInterface $statusCompleteDt): self
    {
        $this->statusCompleteDt = $statusCompleteDt;

        return $this;
    }

    public function getStatusCancelDt(): ?\DateTimeInterface
    {
        return $this->statusCancelDt;
    }

    public function setStatusCancelDt(?\DateTimeInterface $statusCancelDt): self
    {
        $this->statusCancelDt = $statusCancelDt;

        return $this;
    }

    public function getStatusNoshowDt(): ?\DateTimeInterface
    {
        return $this->statusNoshowDt;
    }

    public function setStatusNoshowDt(?\DateTimeInterface $statusNoshowDt): self
    {
        $this->statusNoshowDt = $statusNoshowDt;

        return $this;
    }

    public function getStatusExpiredDt(): ?\DateTimeInterface
    {
        return $this->statusExpiredDt;
    }

    public function setStatusExpiredDt(?\DateTimeInterface $statusExpiredDt): self
    {
        $this->statusExpiredDt = $statusExpiredDt;

        return $this;
    }

    public function getGYear(): ?string
    {
        return $this->gYear;
    }

    public function setGYear(?string $gYear): self
    {
        $this->gYear = $gYear;

        return $this;
    }

    public function getGMonth(): ?int
    {
        return $this->gMonth;
    }

    public function setGMonth(?int $gMonth): self
    {
        $this->gMonth = $gMonth;

        return $this;
    }

    public function getGWeekday(): ?int
    {
        return $this->gWeekday;
    }

    public function setGWeekday(?int $gWeekday): self
    {
        $this->gWeekday = $gWeekday;

        return $this;
    }

    public function getGDayofyear(): ?string
    {
        return $this->gDayofyear;
    }

    public function setGDayofyear(?string $gDayofyear): self
    {
        $this->gDayofyear = $gDayofyear;

        return $this;
    }

    public function getGWait(): ?int
    {
        return $this->gWait;
    }

    public function setGWait(?int $gWait): self
    {
        $this->gWait = $gWait;

        return $this;
    }

    public function getGSeat(): ?int
    {
        return $this->gSeat;
    }

    public function setGSeat(?int $gSeat): self
    {
        $this->gSeat = $gSeat;

        return $this;
    }

    public function getGServe(): ?int
    {
        return $this->gServe;
    }

    public function setGServe(?int $gServe): self
    {
        $this->gServe = $gServe;

        return $this;
    }

    public function getGComplete(): ?int
    {
        return $this->gComplete;
    }

    public function setGComplete(?int $gComplete): self
    {
        $this->gComplete = $gComplete;

        return $this;
    }

    public function getGCancel(): ?int
    {
        return $this->gCancel;
    }

    public function setGCancel(?int $gCancel): self
    {
        $this->gCancel = $gCancel;

        return $this;
    }

    public function getGNoshow(): ?int
    {
        return $this->gNoshow;
    }

    public function setGNoshow(?int $gNoshow): self
    {
        $this->gNoshow = $gNoshow;

        return $this;
    }

    public function getGExpired(): ?int
    {
        return $this->gExpired;
    }

    public function setGExpired(?int $gExpired): self
    {
        $this->gExpired = $gExpired;

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

    public function getNotes(): array
    {
        return $this->notes;
    }

    public function setNotes(?array $notes): self
    {
        $this->notes = $notes;

        return $this;
    }


}