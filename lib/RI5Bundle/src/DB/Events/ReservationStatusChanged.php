<?php
namespace RI5\DB\Events;

use RI5\DB\Entity\Data\ReservationStatus;
use RI5\DB\Entity\Reservation;

/**
 * The Reservation.Status.Changed is dispatched each time an Reservation Status is changed in the system.
 */
class ReservationStatusChanged extends BaseEvent {

    public string $ReservationId;
    public string $CustomerId;
    public string $CustomerPhone;
    public string $PlaceId;
    public string $PlaceName;

    public string $CustomerContactMethod;
    
    public ReservationStatus $Status;
    public const NAME = 'RI5.Reservation.Status.Changed';
    
    public function __construct(Reservation $reservation) {
       
        $this->ReservationId = $reservation->getReservationid();
        $this->CustomerId = $reservation->getCustomer()->getUserid();
        $this->CustomerContactMethod = $reservation->getCustomer()->getContactMethod();
        $this->CustomerPhone = $reservation->getCustomer()->getPhone();
        $this->PlaceId = $reservation->getPlace()->getPlaceid();
        $this->PlaceName = $reservation->getPlace()->getName();
        $this->Status = ReservationStatus::from($reservation->getStatus());
    }

    public function __toString(): string
    {
        return $this::NAME . " - ReservationId:{$this->ReservationId};CustomerPhone:{$this->CustomerPhone};Status:{$this->Status->value}";
    }
  
}
    
   