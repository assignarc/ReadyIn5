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
use RI5\DB\Events\ReservationStatusChanged;
use RI5\DB\Repository\ReservationRepository;
use RI5\Services\Traits\CacheAwareTrait;
use RI5\Services\Traits\ConfigAwareTrait;
use RI5\Services\Traits\EntityAwareTrait;
use RI5\Services\Traits\EventDispatcherTrait;
use RI5\Services\Traits\LoggerAwareTrait;
use RI5\DB\Entity\Data\ReservationId;
use Symfony\Contracts\Cache\ItemInterface;
use RI5\DB\Repository\ReservationArchiveRepository;
use RI5\Services\Traits\UtilityTrait;

class ReservationService extends BaseService 
{
    private PlaceService $placeService;
    private CustomerService $customerService;
    private ReservationArchiveRepository $reservationArchiveRepository;
    
    use EntityAwareTrait;
    use CacheAwareTrait;
    use EventDispatcherTrait;
    use LoggerAwareTrait;
    use ConfigAwareTrait;
    use UtilityTrait;

    public function __construct(ReservationRepository $reservationRepository,ReservationArchiveRepository $reservationArchiveRepository,
                                PlaceService $placeService, CustomerService $customerService) 
    {
        $this->objectRepository = $reservationRepository;
        $this->placeService = $placeService;
        $this->customerService = $customerService;
        $this->reservationArchiveRepository = $reservationArchiveRepository;
    }

   
    /**
     * Clean old reservations
     *
     * @return void
     */
    public function cleanReservations(){
        $minReservationDt = new DateTime(date('Y-m-d H:i:s', strtotime('-500 day')));
        $maxReservationDt = new DateTime(date('Y-m-d H:i:s', strtotime($this->getConfigItem("RI5.RESERVATION.cleanuptime")))); //Default -6 hour
     
        $minStatusDt = $minReservationDt;//date('Y-m-d H:i:s', strtotime('-1 hour')))
        $maxStatusDt = $maxReservationDt;//date('Y-m-d H:i:s', strtotime('-1 hour'));
       
        $inReservationStatuses = array(
                    ReservationStatus::STATUS_SEAT, 
                    ReservationStatus::STATUS_SERVE,
                    ReservationStatus::STATUS_WAIT,
                    ReservationStatus::STATUS_EMPTY,
                    ReservationStatus::STATUS_CALL,
                );
        $notInReservationStatuses = array(
                    ReservationStatus::STATUS_COMPLETE, 
                );
      
        $reservations = $this->objectRepository->getAllByDate($minReservationDt,$maxReservationDt,$minStatusDt,$maxStatusDt, $inReservationStatuses,$notInReservationStatuses);
       
        foreach($reservations as $reservation){
            $fromStatus = $reservation->getStatus();
            $toStatus = ReservationStatus::STATUS_EMPTY;

            switch(ReservationStatus::from($fromStatus)){
                
                case ReservationStatus::STATUS_SEAT:
                case ReservationStatus::STATUS_SERVE:
                    $toStatus = ReservationStatus::STATUS_COMPLETE;//Fulfiled. 
                    break;
                case ReservationStatus::STATUS_EMPTY:
                case ReservationStatus::STATUS_WAIT:
                case ReservationStatus::STATUS_CALL:
                    $toStatus = ReservationStatus::STATUS_EXPIRED;
                    break;
                case ReservationStatus::STATUS_COMPLETE:
                case ReservationStatus::STATUS_CANCEL:  //Customer Canceled
                case ReservationStatus::STATUS_NOSHOW:  //Customer No Show, Marked by Place Owner
                case ReservationStatus::STATUS_EXPIRED: // Place could not fulfill those. 
                default:
                    break;
            }
            $this->logInfo("Cleanup : ResId-{$reservation->getReservationId()};FROM-{$reservation->getStatus()};TO-{$toStatus->value}");
            if($toStatus!=ReservationStatus::STATUS_EMPTY)
                $this->updateReservationStatus($reservation->getReservationId(),$toStatus);
            
            //TODO-Final Status was supposed to be COMPLETE, but now the final status can be COMPLETE, EXPIRED, CANCEL or NOSHOW
            //$this->updateReservationStatus($reservation->getReservationId(),ReservationStatus::STATUS_COMPLETE);
        }
        //TO-DO Publish event when we need to
        //$this->dispatchEvent(new DataNormalizationNeeded("RESERVATION_NORMALIZATION"), DataNormalizationNeeded::NAME);
    }

    /**
     * Get the Wait plan for the current waiting reservation. The function gives back an array of data that can be used to create a plan. 
     *
     * @param string $reservationId
     * @return array
     */
    public function getReservationWaitPlan(string $reservationId) : array {

        $resId = ReservationId::From(intVal($reservationId));

        //uncomment when testing. 
        //$this->cache->invalidateTags(['PLACE.WAIT.'. strval($resId->getPlaceId())]);

        $status= $this->cache->get("PLACE.WAIT." . strval($resId->getPlaceId()) ,  
                                    function (ItemInterface $item) use  ($resId): mixed {
                                        $item->tag(['PLACE.WAIT.'. strval($resId->getPlaceId())]);
                                        $item->expiresAfter(intval($this->getConfigItem("RI5.GEN.cache.statusExpiry")));
                                    
                                        $latestWaitingReservationId = $this->objectRepository->findLastReservationByPlaceId($resId->getPlaceId(), $resId->getQueueId());
                                
                                        $reservation = $this->findOneByReservationId($latestWaitingReservationId->id());
                                        $retArray = [];
                                    
                                        if($reservation){
                                            $retArray["latestresdt"] = $reservation->getReservationDt();
                                            $retArray["latestresid"] = strval($reservation->getReservationid());
                                        }
                                        return $retArray;
                                });
        $status["nextcheck"] = 60000;

        return $status;
    }
   
    /**
     * Create a new Reservation
     *
     * @param Reservation $reservation
     * @param boolean $mustCreateNew
     * @return Reservation
     */
    public function createReservation(Reservation $reservation, bool $mustCreateNew = false): Reservation {

        /*
        Moved to private function for reuse
        $reservationDB = $this->findLatestOneByCustomerId($reservation->getCustomer()->getUserid());

        if($reservationDB){
            $diff = $reservation->getReservationDt()->diff($reservationDB->getReservationDt());
            $hours = ($diff->y * 365 * 24) + ($diff->m * 30 * 24) + ($diff->d * 24) + ($diff->h) + ($diff->i)/60;

           
            if($hours < 12 && $reservationDB->getStatus()=="WAIT") {
                throw new InvalidRequestException("You aleady have a reservation, cancel that before creating a new one");
            }      
            //$reservationDB = null; 
        }
        */
        //$reservationDB = $this->timeCheckforNew($reservation);
        $notes = [];
       
        $notesTag = date_format(new DateTime(),"Y-m-d-H:i:s");
        if(!empty($reservation->getNotes()))
            $notes = $reservation->getNotes();
        else 
            $notes =[]; 

        //TO-DO Change this for the wait algorithm.
        $tableCapacity = 1;
        //$this->logCritical("PlaceId: " . $reservation->getPlace()->getPlaceid());
        $rid = $this->objectRepository->createNextReservationId(0,$reservation->getPlace()->getPlaceid(),$reservation->getQueueid(),$tableCapacity);
        
        $reservation->setReservationId($rid->id());
       
        if(!$reservation->getStatusDt())
            $reservation->setStatusDt(new DateTime());

        $notes[$notesTag]["status"]= $reservation->getStatus();
        $notes[$notesTag]["statusDt"]= date_format($reservation->getStatusDt(),"Y-m-d-H:i:s");
        $notes[$notesTag]["adults"]=$reservation->getAdults();
        $notes[$notesTag]["children"]=$reservation->getChildren();

        $reservation->setNotes($notes);
        
        $this->objectRepository->save($reservation, true); 

         //Dispatch an Event
         $this->dispatchEvent(new ReservationStatusChanged($reservation), ReservationStatusChanged::NAME);
        
        return $this->findReservation($reservation->getCustomer()->getUserid(), $reservation->getPlace()->getPlaceid(), $reservation->getQueueid());
    }

   
    /**
     * Create or update new reservationS
     *
     * @param Reservation $reservation
     * @param boolean $mustCreateNew
     * @return Reservation
     */
    public function createUpdateReservation(Reservation $reservation, bool $mustCreateNew = false): Reservation {

        /* Moved as private function 
        $reservationDB = $this->findLatestOneByCustomerId($reservation->getCustomer()->getUserid());
        if($reservationDB && $reservation->getReservationid() &&  
                    $reservationDB->getReservationid() != $reservation->getReservationid()){
                        
            $diff = $reservation->getReservationDt()->diff($reservationDB->getReservationDt());
            $hours = ($diff->y * 365 * 24) + ($diff->m * 30 * 24) + ($diff->d * 24) + ($diff->h) + ($diff->i)/60;
         
            if($hours < 12 && $reservationDB->getStatus()=="WAIT") {
                throw new InvalidRequestException("You aleady have a reservation, cancel that before creating a new one");
            }      
            //$reservationDB = null; 
        }
        */

        $reservationDB = $this->timeCheckforNew($reservation);
        $notes = [];
       
        
        if(!$reservationDB){
            $reservationDB = new Reservation();
            $reservationDB->setNotes($reservation->getNotes());
            $reservationDB->setStatusDt(new DateTime());
            $reservationDB->setCustomer($reservation->getCustomer());
            $reservationDB->setPlace($reservation->getPlace());
            $reservationDB->setReservationDt($reservation->getReservationDt());
            $reservationDB->setQueueid($reservation->getQueueid());
            $reservationDB->setStatus($reservation->getStatus());
            $reservationDB->setInstructions($reservation->getInstructions());
            $reservationDB->setNotes($notes);

            //TO-DO Change this for the wait algorithm.
            $tableCapacity = 1;
            $rid = $this->objectRepository->createNextReservationId(0,$reservation->getPlace()->getPlaceid(),$reservation->getQueueid(),$tableCapacity);
            $reservationDB->setReservationId($rid->id());
        }

        
        $reservationDB->setStatusDt(new DateTime());
    
        
        if(!empty($reservationDB->getNotes())){
            $notes = $reservationDB->getNotes();
            // foreach ($reservation->getNotes() as $key => $value)
            //     $notes[$notesTag] [$key]=$value;
        }
        else 
            $notes =[];

        //$notesTag = date_format(new DateTime(),"Y-m-d-H:i:s");

        $note = [];
        $note["status"]= $reservationDB->getStatus();
        $note["statusDt"]= $reservationDB->getStatusDt();//date_format($reservationDB->getStatusDt(),"Y-m-d-H:i:s");
        if($reservation->getAdults() != $reservationDB->getAdults()){
            $note["adults"]=$reservationDB->getAdults();
            $reservationDB->setAdults($reservation->getAdults() ?? $reservationDB->getAdults());
        }
        if($reservation->getChildren() != $reservationDB->getChildren()){
            $note["children"]=$reservationDB->getAdults();
            $reservationDB->setChildren($reservation->getChildren() ?? $reservationDB->getChildren());
        }
        if($reservation->getInstructions() != $reservationDB->getInstructions()){
            $note["instructions"]=$reservationDB->getInstructions();
            $reservationDB->setInstructions($reservation->getInstructions() ?? "");
        }

        $notes[] = $note;

        $reservationDB->setReservationDt($reservationDB->getReservationDt() ?? $reservation->getReservationDt());
        $reservationDB->setQueueid($reservationDB->getQueueid() ?? $reservation->getQueueid());
        $reservationDB->setStatus($reservation->getStatus() ?? $reservationDB->getStatus());
        $reservationDB->setNotes($notes);
        
        $this->objectRepository->save($reservationDB,true);

         //Dispatch an Event
         $this->dispatchEvent(new ReservationStatusChanged($reservationDB), ReservationStatusChanged::NAME);

        return $this->findReservation($reservation->getCustomer()->getUserid(), $reservation->getPlace()->getPlaceid(), $reservation->getQueueid());
    }

   /**
    * Retrieve reservation status
    *
    * @param string $reservationId
    * @return ReservationStatus
    */
    public function getReservationStatus(string $reservationId) : ReservationStatus {
        if(!$reservationId || $reservationId=="")
            throw new ReservationNotFoundException("Reservation not found!");

        $status= $this->cache->get("RES.STAT." . $reservationId,  function (ItemInterface $item) use  ($reservationId): mixed {
                    $item->tag(["RES.STAT." . $reservationId, $reservationId]);
                    $item->expiresAfter(intval($this->getConfigItem("RI5.GEN.cache.statusExpiry")));
                    $reservationDB = $this->findOneByReservationId($reservationId);
                    if(!$reservationDB)
                        throw new ReservationNotFoundException("Reservation not found!");
    
                    return ReservationStatus::from($reservationDB->getStatus());
            });

        return $status;
    }

    /**
     * Update reservation status
     *
     * @param string $reservationId
     * @param [type] $toStatus
     * @return Reservation
     */
    public function updateReservationStatus(string $reservationId, ReservationStatus $toStatus=ReservationStatus::STATUS_EMPTY): Reservation {

        //Check for existence
        if(!$reservationId || $reservationId=="")
            throw new ReservationNotFoundException("Reservation not found!");
        if(!$toStatus || $toStatus==ReservationStatus::STATUS_EMPTY)
            throw new ReservationStatusException("Reservation Status is empty or invalid");
        
        $reservationDB = $this->findOneByReservationId($reservationId);

        if(!$reservationDB){
            throw new ReservationNotFoundException("Reservation not found!");
        }
        //if(!$reservationDB->getStatusDt())
        $reservationDB->setStatusDt(new DateTime());
        
        $reservationDB->setStatus($toStatus->value);
       
       //Create Notes for posterity      
        
        //$notesTag = date_format(new DateTime(),"Y-m-d-H:i:s");
        if(!empty($reservationDB->getNotes())){
            $notes = $reservationDB->getNotes();
             // if(!empty($reservationDB->getNotes()))
            //     foreach ($reservationDB->getNotes() as $key => $value)
            //         $notes[$notesTag] [$key]=$value;
        }
        else 
            $notes =[];
        
        $note=[];
        $note["status"]= $reservationDB->getStatus();
        $note["statusDt"]= $reservationDB->getStatusDt();//date_format(new DateTime(),"Y-m-d-H:i:s");
        $note["adults"]= $reservationDB->getAdults();
        $note["children"]= $reservationDB->getChildren();
        $note["instructions"]= $reservationDB->getInstructions() ?? "";

        $notes[] = $note;
       
        
        //Set values
        $reservationDB->setNotes($notes);
        $reservationDB->setStatus($toStatus->value);
        
        
        $this->objectRepository->save($reservationDB,true);

        //Dispatch an Event
        $this->dispatchEvent(new ReservationStatusChanged($reservationDB), ReservationStatusChanged::NAME);

        //Return the updated copy
        return $this->findOneByReservationId($reservationDB->getReservationid());
    }
    
    public function buildReservationJson($rJson):Reservation{
        $reservation=new Reservation();
        $reservation->setAdults($rJson->adults ?? 0);
        $reservation->setChildren($rJson->children ?? 0);
        $reservation->setNotes($rJson->notes ?? array());
        $reservation->setPlaceid($rJson->placeid ?? 0);
        $reservation->setReservationDt($rJson->reservationdt ?? new DateTime());
        $reservation->setQueueid($rJson->queueid ?? 0);
        $reservation->setUserid($rJson->userid ?? 0);
        $reservation->setStatus("WAIT");
        return $reservation;
    }
    public function buildReservation(string $adults,
                string $children, array $notes, int $placeid, DateTimeInterface $reservationDt, int $queueid, int $userid):Reservation{
        $reservation=new Reservation();
        $reservation->setAdults($adults ?? 0);
        $reservation->setChildren($children ?? 0);
        $reservation->setNotes($notes ?? array());
        $reservation->setPlaceid($placeId ?? 0);
        $reservation->setReservationDt($reservationDt ?? new DateTime());
        $reservation->setQueueid($queueId ?? 0);
        $reservation->setUserid($userid ?? 0);
        $reservation->setStatus("WAIT");
        return $reservation;
    }
    /**
     * Finds all Customers
     */
    public function findAll() {

        $data = $this->objectRepository->findAll();
        return $data;
    }
    /*
    *   $currentDateTime = Null means Current List, otherwise it is past. 
    */
    public function findAllByCustomerId(string $customerId, ?DateTime $currentDateTime, 
                                        int $start=0, int $max=100, 
                                        array $inStatuses=[], array $notInStatuses=[]) : ?array {
        
       
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('userid', $customerId));
            //->andWhere(Criteria::expr()->gt('placeid',2));
        if($currentDateTime){
            $criteria =$criteria->andWhere(Criteria::expr()->lt('reservationDt', $currentDateTime));
        }
        else{
            $criteria =$criteria->andWhere(Criteria::expr()->gt('reservationDt',  new DateTime('yesterday')));
        }

        //Build criteria status lists. 
        $inStatusList=[];
        if($inStatuses){
            foreach ($inStatuses as $resStatus) {
                $inStatusList[] = $resStatus->value;
            }
            $criteria = $criteria->andWhere(Criteria::expr()->in("status", $inStatusList)) ;
        }
        $notInnStatusList=[];
        if($notInStatuses){
            foreach ($notInStatuses as $resStatus) {
                $notInnStatusList[] = $resStatus->value;
            }
            $criteria = $criteria->andWhere(Criteria::expr()->notIn("status", $notInnStatusList)) ;
        }
        
        $criteria =$criteria->orderBy(['reservationDt' => 'DESC'])
                ->setFirstResult($start)
                ->setMaxResults($max);
        
        $data = $this->objectRepository->matching($criteria);
        $result = array();
        foreach($data as $key => $value){
            $value->setPlace($this->placeService->findPlaceById($value->getPlaceId()));
            $result[] = $value;
        }
        return $result;
    }
    public function findLatestOneByCustomerId(string $customerId,int $start=0, int $max=1) : ?Reservation {
        
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('userid', $customerId));
            //->andWhere(Criteria::expr()->gt('placeid',2));
      
        $criteria =$criteria->orderBy(['reservationDt' => 'DESC'])
                ->setFirstResult($start)
                ->setMaxResults($max);
        
        $data = $this->objectRepository->matching($criteria);
        $result = array();
        foreach($data as $key => $value){
            $value->setPlace($this->placeService->findPlaceById($value->getPlaceId()));
            $result[] = $value;
        }
        return $result[0];
    }

     /*
    *   $currentDateTime = Null means Current List, otherwise it is past. 
    */
    public function findAllByPlaceSlug(string $placeSlug, string $status="WAIT", DateTimeInterface $currentDateTime, int $start=0, int $max=100) : ?array {
        
        $place = $this->placeService->findPlace($placeSlug);

        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('placeid', $place->getPlaceid()))
            ->andWhere(Criteria::expr()->eq('status',$status));
        
        if($currentDateTime){
            $criteria =$criteria->andWhere(Criteria::expr()->lt('reservationDt', $currentDateTime));
        }
        else{
            $criteria =$criteria->andWhere(Criteria::expr()->gt('reservationDt', $currentDateTime));
        }
        $criteria =$criteria->orderBy(['reservationDt' => 'DESC'])
                ->setFirstResult($start)
                 ->setMaxResults($max);
        
        $data = $this->objectRepository->matching($criteria);
        $result = array();
        foreach($data as $key => $value){
            $value->setPlace($this->placeService->findPlaceById($value->getPlaceId()));
            $cust = $this->customerService->findCustomerByUserId($value->getUserId());
            $cust->setFirstname(UtilityTrait::Obfuscate($cust->getFirstname()));
            $cust->setLastName(UtilityTrait::Obfuscate($cust->getLastname()));
            $cust->setPhone(UtilityTrait::Obfuscate($cust->getPhone(),2,4,"*"));
            $value->setCustomer($cust);
            $result[] = $value;
        }
        return $result;
    }
     /*
    *   $reservationId 
    */
    public function findOneByReservationId(string $reservationId) : ?Reservation {
        return  $this->objectRepository->findOneByReservationid($reservationId);
    }
    public function findReservation(string $customerUserId, string $placeId, string $queueId): ?Reservation {

        $reservation = $this->objectRepository
                                ->findOneBy(array(
                                    "userid"=>$customerUserId ,
                                    "placeid" => $placeId, 
                                    "queueid" => $queueId
                                 ));
            
        return $reservation;
    }
    
    /**
     * Time check, can't create a new if there is already one waiting. If wait passed 12 hours allow new creation. 
     *
     * @param Reservation $reservation
     * @return Reservation|null
     */
    private function timeCheckforNew(Reservation $reservation):?Reservation {
        $reservationDB = $this->findLatestOneByCustomerId($reservation->getCustomer()->getUserid());

        if($reservationDB && $reservation->getReservationid() &&  
                    $reservationDB->getReservationid() != $reservation->getReservationid()){
                        
            $diff = $reservation->getReservationDt()->diff($reservationDB->getReservationDt());
            $hours = ($diff->y * 365 * 24) + ($diff->m * 30 * 24) + ($diff->d * 24) + ($diff->h) + ($diff->i)/60;
         
            if($hours < 12 && $reservationDB->getStatus()=="WAIT") {
                throw new InvalidRequestException("You aleady have a reservation, cancel that before creating a new one", 9501, [], null);
            }      
        }
        return $reservationDB;
    }
}