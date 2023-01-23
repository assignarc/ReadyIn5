<?php
namespace RI5\DB\Events;

/**
 * The Time.Up is dispatched each time an Reservation Status is changed in the system.
 */
class TimeUp extends BaseEvent {

    /**
     * Time interval in minutes
     *
     * @var string
     */
    public string $TimeInterval;
    public const NAME = 'RI5.Time.Up';
    
    public function __construct(string $time="") {
        $this->TimeInterval = $time;
     
    }

    public function __toString(): string
    {
        return $this::NAME . " - {$this->TimeInterval}";
    }
  
}
    
   