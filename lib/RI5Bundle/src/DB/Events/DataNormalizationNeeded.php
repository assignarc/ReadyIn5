<?php
namespace RI5\DB\Events;

/**
 * The Time.Up is dispatched each time an Reservation Status is changed in the system.
 */
class DataNormalizationNeeded extends BaseEvent {

   
    public string $NormalizationType="";
    public const NAME = 'RI5.Data.Normalization.Needed';
    
    public function __construct(string $normalizationType="") {
        $this->NormalizationType= $normalizationType;
    }
    public function __toString(): string
    {   
        return $this::NAME . " - Normalization Type :{$this->NormalizationType}";
    }
}
    
   