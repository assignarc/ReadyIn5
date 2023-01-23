<?php
namespace RI5\Services\Traits;

use Psr\Log\LogLevel;
use RI5\DB\Events\BaseEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait EventDispatcherTrait{
    use LoggerAwareTrait;
    
    private EventDispatcherInterface $eventDispatcher;

    #[Required]
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }   


    public function dispatchEvent(BaseEvent $event,?string $eventName =null){
        $this->eventDispatcher->dispatch($event, $eventName);
        $this->logInfo("EventDispatched: " . $eventName);
    }
}
