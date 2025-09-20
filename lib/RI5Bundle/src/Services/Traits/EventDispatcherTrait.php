<?php
namespace RI5\Services\Traits;

use RI5\DB\Events\BaseEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait EventDispatcherTrait{
    use LoggerAwareTrait;
    
    private EventDispatcherInterface $eventDispatcher;

    #[Required]
    /**
     * Summary of setEventDispatcher
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @return void
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }   
 
    /**
     * Summary of dispatchEvent
     * @param \RI5\DB\Events\BaseEvent $event
     * @param mixed $eventName
     * @return void
     */
    public function dispatchEvent(BaseEvent $event,?string $eventName =null){
        $this->eventDispatcher->dispatch($event, $eventName);
        $this->logInfo("EventPUBLISH: {$eventName}");
    }
}
