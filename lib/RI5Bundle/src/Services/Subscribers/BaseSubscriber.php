<?php
namespace RI5\Services\Subscribers;

use RI5\DB\Events\BaseEvent;
use RI5\Services\Traits\LoggerAwareTrait;;

class BaseSubscriber 
{
    use LoggerAwareTrait;
   
    public function logEvent(BaseEvent $event, string $status)
    {
       $this->logInfo("Event{$status}: " . $event);
    }
}
