<?php
namespace RI5\DB\Events;

use RI5\DB\Entity\Place;

/**
 * The Place.Updated is dispatched each time an Reservation Status is changed in the system.
 */
class PlaceUpdated extends BaseEvent {

    public string $PlaceId;
    public string $PlaceSlug;
    public const NAME = 'RI5.Place.Updated';
    
    public function __construct(Place $place) {
       
        $this->PlaceId = $place->getPlaceid();
        $this->PlaceSlug = $place->getSlug();
     
    }
    public function __toString(): string
    {   
        return $this::NAME . " - Place Id:{$this->PlaceId}|PlaceSlug:{$this->PlaceSlug}";
    }
  
}
    
   