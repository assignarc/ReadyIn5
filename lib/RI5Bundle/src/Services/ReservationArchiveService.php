<?php

namespace RI5\Services;

use RI5\DB\Entity\Data\ReservationStatus;
use RI5\DB\Entity\Reservation;
use RI5\Exception\InvalidRequestException;
use RI5\Exception\ReservationNotFoundException;
use RI5\Exception\ReservationStatusException;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\Criteria;
use Exception;
use Psr\Log\LoggerTrait;
use RI5\DB\Events\ReservationStatusChanged;
use RI5\DB\Repository\ReservationRepository;
use RI5\Services\Traits\CacheAwareTrait;
use RI5\Services\Traits\ConfigAwareTrait;
use RI5\Services\Traits\EntityAwareTrait;
use RI5\Services\Traits\EventDispatcherTrait;
use RI5\Services\Traits\LoggerAwareTrait;
use RI5\DB\Entity\Data\ReservationId ;
use RI5\DB\Entity\Data\WLConstants;
use RI5\DB\Entity\ReservationArchive;
use Symfony\Contracts\Cache\ItemInterface;
use RI5\DB\Repository\ReservationArchiveRepository;
use RI5\Services\Traits\UtilityTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ConfiguratorTrait;

class ReservationArchiveService extends BaseService 
{
    private PlaceService $placeService;
    private CustomerService $customerService;
    private ReservationRepository $reservationRepository;
    
    use EntityAwareTrait;
    use CacheAwareTrait;
    use EventDispatcherTrait;
    use LoggerAwareTrait;
    use ConfigAwareTrait;
    use UtilityTrait;
   

    public function __construct(ReservationRepository $reservationRepository,ReservationArchiveRepository $reservationArchiveRepository,
                                PlaceService $placeService, CustomerService $customerService) 
    {
        $this->objectRepository = $reservationArchiveRepository;
        $this->placeService = $placeService;
        $this->customerService = $customerService;
        $this->reservationRepository = $reservationRepository;
    }

  
    /**
     * Archive Reservations
     *
     * @return void
     */
    public function archiveReservations(){
        $minReservationDt = new DateTime(date('Y-m-d H:i:s', strtotime('-500 day')));
        $maxReservationDt = new DateTime(date('Y-m-d H:i:s', strtotime($this->getConfigItem("RI5.RESERVATION.archivetime")))); //Default -12 hour
     
        $minStatusDt = $minReservationDt;//date('Y-m-d H:i:s', strtotime('-1 hour')))
        $maxStatusDt = $maxReservationDt;//date('Y-m-d H:i:s', strtotime('-1 hour'));
       
        $inReservationStatuses = array(
                    ReservationStatus::STATUS_COMPLETE, 
                    ReservationStatus::STATUS_EXPIRED,
                    ReservationStatus::STATUS_CANCEL,
                    ReservationStatus::STATUS_NOSHOW,
                );
      
        $reservations = $this->reservationRepository->getAllByDate($minReservationDt,$maxReservationDt,$minStatusDt,$maxStatusDt, $inReservationStatuses);
       
        foreach($reservations as $reservation){
            $this->entityManager->getConnection()->beginTransaction(); 
            try{
                $ra = new ReservationArchive();
                $ra->setAdults($reservation->getAdults());
                $ra->setChildren($reservation->getChildren());
                $ra->setInstructions($reservation->getInstructions());
                $ra->setNotes($reservation->getNotes());
                $ra->setPlaceid($reservation->getPlaceid());
                $ra->setQueueid($reservation->getQueueid());
                $ra->setReservationDt($reservation->getReservationDt());
                $ra->setUserid($reservation->getUserid());
                $ra->setReservationId($reservation->getReservationId());
                
                $notes = $reservation->getNotes();
                foreach($notes as $key => $value) {
                    //$status = $value["status"] ? "";
                    $status = isset($value["status"]) ? ReservationStatus::from($value["status"]) : ReservationStatus::STATUS_EMPTY;
                    $datetime =  DateTime::createFromFormat("Y-m-d-H:i:s", $key);
                    //$datetimeStr = $datetime ? $datetime->format('Y-m-d H:i:s') : WLConstants::NONE;
                    $datetime =  $datetime ? $datetime:  null;

                    switch($status){
                        case ReservationStatus::STATUS_SEAT:
                            $ra->setStatusSeatDt($datetime);
                            break;
                        case ReservationStatus::STATUS_SERVE:
                            $ra->setStatusServeDt($datetime);
                            break;
                        case ReservationStatus::STATUS_WAIT:
                            $ra->setStatusWaitDt($datetime);
                            break;
                        case ReservationStatus::STATUS_CALL:
                            $ra->setStatusCallDt($datetime);
                            break;
                        case ReservationStatus::STATUS_COMPLETE:
                            $ra->setStatusCompleteDt($datetime);
                            break;
                        case ReservationStatus::STATUS_CANCEL:  //Customer Canceled
                            $ra->setStatusCancelDt($datetime);
                            break;
                        case ReservationStatus::STATUS_NOSHOW:  //Customer No Show, Marked by Place Owner
                            $ra->setStatusNoshowDt($datetime);
                            break;
                        case ReservationStatus::STATUS_EXPIRED: // Place could not fulfill those. 
                            $ra->setStatusExpiredDt($datetime);
                            break;
                        case ReservationStatus::STATUS_EMPTY:
                        default:
                            break;
                    }

                    $this->logCritical($reservation->getReservationId() . "-Notes:" . $key . "|" .$status->value . "|");// . var_export($ra,true));
                }
                
                $this->objectRepository->save($ra,true);
                $this->reservationRepository->remove($reservation,true);

                $this->entityManager->getConnection()->commit(); 
            }
            catch(Exception $ex){
                $this->logException($ex);
                $this->entityManager->getConnection()->rollBack(); 
            } 
        }
     
    }
   
}