<?php
namespace RI5\DB\Events;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * The RI5.Base.Event is dispatched each time an Reservation Status is changed in the system.
 */ 
abstract class BaseEvent extends Event
{
    use RI5EventTrait;
   
    public const NAME ="RI5.Base.Event";
    public function __construct()
    {
        
    }
    abstract public function __toString() : string;
}