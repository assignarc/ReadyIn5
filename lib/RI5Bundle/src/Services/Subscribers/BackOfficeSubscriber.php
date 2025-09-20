<?php
namespace RI5\Services\Subscribers;

use Exception;
use RI5\DB\Events\DataNormalizationNeeded;
use RI5\DB\Events\TimeUp;
use RI5\Services\ReservationArchiveService;
use RI5\Services\ReservationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Service\Attribute\Required;

class BackOfficeSubscriber extends BaseSubscriber implements EventSubscriberInterface
{
    private ReservationService $reservationService;
    private ReservationArchiveService $reservationArchiveService;
   
    #[Required]
    public function setObjectRepository(ReservationService $reservationService, ReservationArchiveService $reservationArchiveService){
        $this->reservationService = $reservationService;
        $this->reservationArchiveService = $reservationArchiveService;
    }
   
    public static function getSubscribedEvents()
    {
        return [
            TimeUp::NAME => 'onTimeUp',
            DataNormalizationNeeded::NAME => 'onDataNormalizationNeeded',
        ];
    }
    /**
     * Summary of onTimeUp
     * @param \RI5\DB\Events\TimeUp $event
     * @return void
     */
    public function onTimeUp(TimeUp $event){
       
        try{
            $this->logEvent($event,"Received");
            //Call Cleanup of the Reservations
            $this->reservationService->cleanReservations();
            $this->logEvent($event,"Processed");
        }
        catch(Exception $ex){
            $this->logException($ex);
        }
    }
    /**
     * Summary of onDataNormalizationNeeded
     * @param \RI5\DB\Events\DataNormalizationNeeded $event
     * @return void
     */
    public function onDataNormalizationNeeded(DataNormalizationNeeded $event){
       
        try{
            $this->logEvent($event,"Received");
            //Call Cleanup of the Reservations
            $this->reservationArchiveService->archiveReservations();
            $this->logEvent($event,"Processed");
        }
        catch(Exception $ex){
            $this->logException($ex);
        }
    }
}