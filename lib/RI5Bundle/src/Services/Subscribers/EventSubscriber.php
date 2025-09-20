<?php
namespace RI5\Services\Subscribers;

use Exception;
use RI5\DB\Entity\Data\ReservationStatus;
use RI5\DB\Events\PlaceUpdated;
use RI5\DB\Events\ReservationStatusChanged;
use RI5\Services\Traits\CacheAwareTrait;
use RI5\Services\Traits\MessengerTrait;
use RI5\Services\Traits\UtilityTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventSubscriber extends BaseSubscriber implements EventSubscriberInterface
{
    
    use CacheAwareTrait;
    use MessengerTrait;
    use UtilityTrait;
    
    public static function getSubscribedEvents()
    {
        return [
            ReservationStatusChanged::NAME => 'onReservationStatusChanged',
            PlaceUpdated::NAME => 'onPlaceUpdated',
        ];
    }
   
    public function onPlaceUpdated(PlaceUpdated $event){
        try{
            $this->logEvent($event,"RECEIVE");
            //Invalidate Place Cache
            $this->cache->invalidateTags(["PLACE.INFO." . strval($event->PlaceId)]);
            $this->cache->invalidateTags(["PLACE.INFO." . strval($event->PlaceSlug)]);
            $this->logEvent($event,"PROCESS");
        }
        catch(Exception $ex){
            $this->logException($ex);
        }
    }

    public function onReservationStatusChanged(ReservationStatusChanged $event)
    {
        try{
           
            $this->logEvent($event,"RECEIVE");
            //Send message to customer
            switch($event->Status){
                case ReservationStatus::STATUS_WAIT:
                case ReservationStatus::STATUS_CALL:
                    //Invalidate the wait plan for other waiting reservations. 
                    $this->cache->invalidateTags(["PLACE.WAIT." . strval($event->PlaceId)]);
                case ReservationStatus::STATUS_CANCEL:
                    $message =  UtilityTrait::Substitute("Your reservation at {{0}} changed to {{1}}",
                                        [UtilityTrait::MaxLength($event->PlaceName,20), $event->Status->value]);
                    $this->logDebug($message);
                    $this->messenger->sendMessage($event->CustomerPhone,$message,$event->CustomerContactMethod);
                    break;
                default:
                    break;
            }
            //Remove from cache with Tag
            //Invalidate the cache status for reservation
            $this->cache->invalidateTags(["RES.STAT.".$event->ReservationId]);
            $this->logEvent($event,"PROCESS");
        }
        catch(Exception $ex){
            $this->logException($ex);
        }
    }
}