<?php
namespace RI5\Services\Subscribers;

use Psr\Log\LogLevel;
use RI5\DB\Events\BaseEvent;
use RI5\Services\Traits\LoggerAwareTrait;

abstract class BaseSubscriber 
{
    use LoggerAwareTrait;
    private string $LOG_LEVEL = LogLevel::DEBUG;
    public function logEvent(BaseEvent $event, string $status)
    {
       $this->logMessage(message:"Event{$status}: " . $event->__toString(),level: $this->LOG_LEVEL);
    }
}
