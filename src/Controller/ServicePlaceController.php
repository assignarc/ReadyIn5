<?php

namespace App\Controller;

use RI5\DB\Entity\Data\ReservationStatus;
use RI5\DB\Entity\Data\WLConstants;
use RI5\DB\Entity\Reservation;
use RI5\DB\Entity\Place;
use RI5\DB\Entity\PlaceHolidays;
use RI5\DB\Entity\PlaceOwner;
use RI5\DB\Entity\PlaceSchedule;
use RI5\Exception\BaseException;
use RI5\Exception\InvalidRequestException;
use RI5\Exception\PlaceInvalidRequestException;
use RI5\Exception\PlaceNotFoundException;
use RI5\Exception\SecurityException;
use RI5\Services\CustomerService;
use RI5\Services\ReservationService;
use RI5\Services\PlaceService;
use DateTime;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;
use RI5\Exception\PlaceOwnerNotFoundException;

//https://ourcodeworld.com/articles/read/1386/how-to-generate-the-entities-from-a-database-and-create-the-crud-automatically-in-symfony-5

#[Route('/svc')]
class ServicePlaceController extends BaseController 
{
   

    #[Route('/place/{placeSlug}/info', name: 'PLACE_API_ProfileInfoGet' , methods: ["GET"])]
    public function getPlaceInfo(string $placeSlug, PlaceService $placeService): Response
    { 
        $response = new Response();
        
        $response->headers->set('Content-Type', 'application/json');
        try{
            //Get cached response from service
            $place = $placeService->findPlace($placeSlug);
            if(!$place)
                throw new PlaceNotFoundException("Place not found.");
          
            $this->responseDetails->setMessage("Place found");
            $this->responseDetails->addDetail("place",$place);  
        }
        catch(Exception $ex){
            $this->logException($ex);
            $ex = BaseException::CREATE($ex);
            $this->responseDetails->setCode($ex->getCode());
            $this->responseDetails->setMessage($ex->getMessage());
            $this->responseDetails->addDetail("exception", $ex);
            $response->setStatusCode($ex->getResponseCode());
        }
        $response->setContent(json_encode($this->responseDetails));
        return $response;
    }
    #[Route('/place/{placeSlug}/reservations/current/{status}', name: 'PLACE_API_ReservationCurrentGet' , methods: ["GET"])]
    public function placeReservations(string $placeSlug, string $status,PlaceService $placeService, ReservationService $reservationService): Response
    { 
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        try{
            $this->checkPlacePermissions([WLConstants::AUTHROLE_PLACE_OWNER, WLConstants::AUTHROLE_PLACE_MANAGER],$placeSlug);

            $place = $placeService->findPlace($placeSlug);
            $reservations = $reservationService->findAllByPlaceSlug($place->getSlug(), $status,new DateTime('now'));
            $this->responseDetails->addDetail("reservations", $reservations);
            $response->setStatusCode(Response::HTTP_OK);
        }
        catch(Exception $ex){
            $this->logException($ex);
            $ex = BaseException::CREATE($ex);
            $this->responseDetails->setCode($ex->getCode());
            $this->responseDetails->setMessage($ex->getMessage());
            $this->responseDetails->addDetail("exception", $ex->__toString());
            $response->setStatusCode($ex->getResponseCode());
        } 
        $response->setContent(json_encode($this->responseDetails));
        return $response;
        
    }
    #[Route('/place/{placeSlug}/reservations/status', name: 'PLACE_API_ReservationStatusUpdate' , methods: ["POST"])]
    public function updateReservationStatus(string $placeSlug,ReservationService $reservationService): Response
    { 
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        try{
            
            // if($placeSlug!=$this->getSessionParm(WLConstants::SESSION_REST_ADMIN_TOKEN, "EMPTY-PLACE"))
            //     throw new SecurityException("User unauthorized!");
            
            $this->checkPlacePermissions([WLConstants::AUTHROLE_PLACE_OWNER, WLConstants::AUTHROLE_PLACE_MANAGER],
                                    $placeSlug);
                                    
            $status = $this->postParm("wlToStatus",null);
            $reservationId = $this->postParm("reservationId",null);

            $reservation = $reservationService->updateReservationStatus($reservationId, ReservationStatus::from($status));
            
            $this->responseDetails->setMessage("Success! Reservation set to " .  $status);
            $response->setStatusCode(Response::HTTP_OK);
        }
        catch(Exception $ex){
            $this->logException($ex);
            $ex = BaseException::CREATE($ex);
            $this->responseDetails->setCode($ex->getCode());
            $this->responseDetails->setMessage($ex->getMessage());
            $this->responseDetails->addDetail("exception", $ex->__toString());
            $response->setStatusCode($ex->getResponseCode());
        } 

        $response->setContent(json_encode($this->responseDetails));
        return $response;
        
    }

    #[Route('/place/{placeSlug}/reservation', name: 'PLACE_API_CustomerReservationCreate' , methods: ["POST"])]
    public function createCustomerReservation(string $placeSlug,
                        ReservationService $reservationService,
                        CustomerService $customerService,
                        PlaceService $placeService
                        ): Response
    { 
        $response = new Response();
        
        $response->headers->set('Content-Type', 'application/json');
        try{
            // if("1"!=$this->getSessionParm(WLConstants::SESSION_AUTH_TOKEN, "0"))
            //     throw new SecurityException("User unauthorized");
            
            // if($placeSlug!=$this->getSessionParm(WLConstants::SESSION_REST_TOKEN, "EMPTY-PLACE"))
            //     throw new SecurityException("User unauthorized or Invalid place");

            $this->checkCustomerPermissions([WLConstants::AUTHROLE_CUSTOMER],$placeSlug);

            $place = $placeService->findPlace($placeSlug,false);
            $customer = $customerService->findCustomer($this->getSessionParm(WLConstants::S_CUST_PHONE, WLConstants::NONE));
            if(!$customer || !$place)
                throw new InvalidRequestException("Incorrect User or Place.");

            $reservation = new Reservation();
            $reservation->setAdults($this->postParm("adults",1));
            $reservation->setChildren($this->postParm("children",0));
            $reservation->setInstructions($this->postParm("specialnotes",null));
            $reservation->setCustomer($customer);
            $reservation->setPlace($place);
            $reservation->setStatus(ReservationStatus::STATUS_WAIT->value);
            $reservation->setReservationDt(new DateTime());
            $reservation->setQueueid(1);
 
            $reservation = $reservationService->createReservation($reservation,false);
            $this->responseDetails->setMessage("Success! Table reserved at " . $place->getName() );
            $this->responseDetails->addDetail('reservation', $reservation);
            
            $response->setStatusCode(Response::HTTP_OK);
        }
        catch(Exception $ex){
            $this->logException($ex);
            $ex = BaseException::CREATE($ex);
            $this->responseDetails->setCode($ex->getCode());
            $this->responseDetails->setMessage($ex->getMessage());
            $this->responseDetails->addDetail("exception", $ex);
            $response->setStatusCode($ex->getResponseCode());
        } 

        $response->setContent(json_encode($this->responseDetails));
        return $response;
        
    }

    #[Route('/place/{placeSlug}/profile/{reqType}', name: 'PLACE_API_ProfileMetaGet' , methods: ["GET"])]
    public function getPlaceMeta(string $placeSlug,string $reqType, PlaceService $placeService): Response
    { 
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        try{
            // if($placeSlug!=$this->getSessionParm("REST-ADMIN-Hash", "EMPTY-PLACE"))
            //     throw new SecurityException("User unauthorized");

            //$this->checkPlacePermissions([WLConstants::AUTHROLE_PLACE_OWNER, WLConstants::AUTHROLE_PLACE_MANAGER],$placeSlug);

            $place = $placeService->findPlace($placeSlug, true);
            
            if(!$place){
                throw new InvalidRequestException("Place not found or you are not authorized to manage it");
            }
            switch ($reqType) {
                case "holidays":
                    $this->responseDetails->addDetail("holidays", $placeService->getPlaceMeta($placeSlug,$reqType));
                    break;
                case "users":
                    $this->responseDetails->addDetail("users", $place->getPlaceUsers());
                    break;
                case "queues":
                    $this->responseDetails->addDetail("queues", $place->getPlaceQueues());
                    break;
                case "schedule":
                    $count = ($place->getPlaceSchedules() ? count($place->getPlaceSchedules()): 0);
                    if($count<7){
                        $placeService->createDefaultPlaceSchedule($place);
                        $place = $placeService->findPlace($this->getSessionParm($placeSlug,WLConstants::NONE));
                    }
                    $this->responseDetails->addDetail("schedule", $placeService->getPlaceMeta($placeSlug,$reqType));
                    break;
                case "meta":
                    $this->responseDetails->addDetail("place", $place);
                    break;
                default:
                    throw new InvalidRequestException("Invalid Place request.");
                    break;
            }
            $response->setStatusCode(Response::HTTP_OK);
        }
        catch(Exception $ex){
            $this->logException($ex);
            $ex = BaseException::CREATE($ex);
            $this->responseDetails->setCode($ex->getCode());
            $this->responseDetails->setMessage($ex->getMessage());
            $this->responseDetails->addDetail("exception", $ex->__toString());
            $response->setStatusCode($ex->getResponseCode());
        } 
        $response->setContent(json_encode($this->responseDetails));
        return $response;
        
    }

    /**/
    /**
     * Essentially a GET but need to make it for  placeSlug or Phone or ownerId
     *
     * @param PlaceService $placeService
     * @return Response
     */
    #[Route('/place/owner/info', name: 'PLACE_API_OwnerInfoGet' , methods: ["POST"])]
    public function getPlaceOwnerInfo(PlaceService $placeService): Response
    { 
        $response = new Response();
        
        $response->headers->set('Content-Type', 'application/json');
        try{
            //Get cached response from service
            $placeOwner = $placeService->getPlaceOwner($this->postParm("ownerPhoneNumber",""));
            if(!$placeOwner)
                throw new PlaceOwnerNotFoundException("Place not found.");
          
            $this->responseDetails->setMessage("Place owner profile found");
            $this->responseDetails->addDetail("placeOwner",$placeOwner);  
        }
        catch(Exception $ex){
            $this->logException($ex);
            $ex = BaseException::CREATE($ex);
            $this->responseDetails->setCode($ex->getCode());
            $this->responseDetails->setMessage($ex->getMessage());
            $this->responseDetails->addDetail("exception", $ex);
            $response->setStatusCode($ex->getResponseCode());
        }
        $response->setContent(json_encode($this->responseDetails));
        return $response;
    }

    
    #[Route('/place/owner/dashboard', name: 'PLACE_API_OwnerDashboardGet' , methods: ["GET"])]
    public function getPlaceOwnerDashboard(PlaceService $placeService): Response
    { 
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        try{
            $this->checkPlacePermissions([WLConstants::AUTHROLE_PLACE_OWNER, WLConstants::AUTHROLE_PLACE_MANAGER]);

            $owner = $placeService->getPlaceOwner($this->getSessionParm(WLConstants::S_PLACE_PHONE,""));
            if(!$owner)
                throw new SecurityException("Access denied.");
            $places = $placeService->findPlacesByOwner($owner);
          
            $this->responseDetails->addDetail("places", $places);
            $this->responseDetails->addDetail("slugString",$this->getSessionParm(WLConstants::S_PLACE_PLACESLUGS,WLConstants::NONE));
          
            $this->responseDetails->setMessage("Place owner profile found");
         
        }
        catch(Exception $ex){
            $this->logException($ex);
            $ex = BaseException::CREATE($ex);
            $this->responseDetails->setCode($ex->getCode());
            $this->responseDetails->setMessage($ex->getMessage());
            $this->responseDetails->addDetail("exception", $ex);
            $response->setStatusCode($ex->getResponseCode());
        }
        $response->setContent(json_encode($this->responseDetails));
        return $response;
    }
    
    #[Route('/place/{placeSlug}/profile/{reqType}', name: 'PLACE_API_ProfileMetaUpdate' , methods: ["POST","DELETE"])]
    public function setPlaceMeta(string $placeSlug,string $reqType,PlaceService $placeService): Response
    { 
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        try{

            $this->checkPlacePermissions([WLConstants::AUTHROLE_PLACE_OWNER, WLConstants::AUTHROLE_PLACE_MANAGER], $placeSlug);

            $place = $placeService->findPlace($placeSlug);
           
            switch ($reqType) {
                case 'holidays':
                    if ($this->request->isMethod('post')) {
                        $placeHoliday = new PlaceHolidays();
                        $placeHoliday->setPlaceId($place->getPlaceId());
                        $placeHoliday->setHolidayDate(new DateTime($this->postParm("holidayDate","")));
                        $placeHoliday->setHolidayName($this->postParm("holidayName",""));
                        $placeHoliday->setSpecialNote($this->postParm("specialNote",""));
                        $placeService->createUpdatePlaceHolidays($placeHoliday, $this->postParm("holidayid",null));
                        $this->responseDetails->addDetail("holidays",$place->getPlaceHolidays());
                        $this->responseDetails->setMessage("Holiday change request completed.");
                    }
                    if ($this->request->isMethod('delete')) {
                        if($place->getPlaceid() != $this->postParm("placeid","")){
                            throw new PlaceInvalidRequestException("Invalid place for deleting holiday");
                        }
                        $placeService->removeMetaEntity("holidays",$this->postParm("holidayid",""));
                        $this->responseDetails->setMessage("Holiday removed! ");
                    }
                    break;
                case 'users':
                    $this->responseDetails->addDetail("users",$place->getPlaceUsers());
                    $this->responseDetails->setMessage("User change request received, not completed.");
                    break;
                case 'Queues':
                    $this->responseDetails->addDetail("queues",$place->getPlaceQueues());
                    $this->responseDetails->setMessage("Queue change request received, not completed.");
                    break;
                case 'schedule':
                    if ($this->request->isMethod('post')) {
                        $json = json_decode($this->request->getContent());
                        
                        $placeSchedule = new PlaceSchedule();
                        $placeSchedule->setPlaceid($place->getPlaceid());
                        $placeSchedule->setPlaceid($this->postParm("placeid",""));
                        $placeSchedule->setOpenTime($json->openTime);
                        $placeSchedule->setCloseTime($json->closeTime);
                        $placeSchedule->setShift($this->postParm("shift",""));
                        $placeSchedule->setDay($this->postParm("day",""));
                        $placeService->createUpdatePlaceSchedule($placeSchedule,$this->postParm("scheduleid",null));

                    }
                    $this->responseDetails->addDetail("schedule",$place->getPlaceSchedules());
                    $this->responseDetails->setMessage("Schedule change request completed.");
                    break;
                case 'owner':
                   
                    if ($this->request->isMethod('post')) {
                        //$json = $this->postJson();
                        $placeOwner = new PlaceOwner();

                        $placeOwner->setPlaces([$place]);
                        $placeOwner->setName($this->postParm("name",""));
                        $placeOwner->setAddressline1($this->postParm("addressline1",""));
                        $placeOwner->setAddressline2($this->postParm("addressline2",""));
                        $placeOwner->setAddressline3($this->postParm("addressline3",""));
                        $placeOwner->setCity($this->postParm("city",""));
                        $placeOwner->setState($this->postParm("state",""));
                        $placeOwner->setCountry($this->postParm("country",""));
                        $placeOwner->setPostalcode($this->postParm("postalcode",""));

                        $placeOwner->setPhone($this->postParm("phone",""));
                        $placeOwner->setEmail($this->postParm("email",""));

                        $placeOwner->setPhonevalidated(true);
                        $placeOwner->setEmailvalidated(false);
                      
                        $placeOwner= $placeService->createUpdatePlaceOwner($placeOwner);
                        
                        //TO-DO
                        //Remove PlaceData from Response. 
                        //$placeOwner->setPlace(null);
                        //$response->setContent(json_encode($placeOwner));
                        $this->responseDetails->addDetail("owner",$placeOwner);
                        $this->responseDetails->setMessage("Owner change request completed.");

                    }
                  
                    break;
                case 'meta':
                    $json = json_decode($this->request->getContent());
                    if(!$place){
                        $place=new Place(); 
                        $place->setSlug($json->slug);
                    }
                    $place->setName($json->name);
                    $place->setAddressline1($json->addressline1);
                    $place->setAddressline2($json->addressline2);
                    $place->setAddressline3($json->addressline3);
                    $place->setCity($json->city);
                    $place->setState($json->state);
                    $place->setCountry($json->country);
                    $place->setPostalcode($json->postalcode);
                    $place->setPhone($json->phone);
                    
                    $place = $placeService->createUpdatePlace($place);

                    $this->responseDetails->addDetail("place",$place);
                    $this->responseDetails->setMessage("Place update request completed.");
                    break;
                default:
                    throw new InvalidRequestException("Invalid Request");
                    break;
            }
            $response->setStatusCode(Response::HTTP_OK);
        }
        catch(Exception $ex){
            $this->logException($ex);
            $ex = BaseException::CREATE($ex);
            $this->responseDetails->setCode($ex->getCode());
            $this->responseDetails->setMessage($ex->getMessage());
            $this->responseDetails->addDetail("exception", $ex->__toString());
            $response->setStatusCode($ex->getResponseCode());
        } 

        $response->setContent(json_encode($this->responseDetails));
        return $response;
        
    }
  
    #[Route('/place/new', name: 'PLACE_API_PlaceCreate' , methods: ["POST"])]
    public function createPlace(PlaceService $placeService): Response
    { 
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        try{
            // if($this->getSessionParm(WLConstants::SESSION_AUTH_TOKEN,"0") != "1")
            //     throw new SecurityException("User unauthorized");
           
            $json= $this->postJson();
            $place = new Place();
            $place->setName($json->name);
            $place->setAddressdata(json_decode($this->request->getContent()));

            $place->setAddressline1($json->address->home . ", " . $json->address->street);
            
            $place->setAddressline2("");
            if(isset($json->address->landmark) &&  $json->address->landmark !="")
                $place->setAddressline2("Near " . $json->address->landmark . ",");
            if(isset($json->address->intersection) &&  $json->address->intersection !="")
                $place->setAddressline2($place->getAddressline2() . " " . $json->address->intersection);
            
            $place->setAddressline3("");
            if(isset($json->address->neighborhood) &&  $json->address->neighborhood !="")
                $place->setAddressline3($json->address->neighborhood . ", ");
            if(isset($json->address->sublocality) &&  $json->address->sublocality !="")
                $place->setAddressline3($place->getAddressline3() . $json->address->sublocality);

            $place->setPhone(isset($json->phone) ?  str_replace(" ","",$json->phone) : "");

            $place->setCity($json->address->city);
            $place->setState($json->address->region);
            $place->setPostalcode($json->address->postalcode);
            $place->setCountry($json->address->country);
            $place->setSlug($json->placeId);
            
            /**Set a new Owner */
            /**Place is NOT set, service will set that up */
            $placeOwner = new PlaceOwner();
            $placeOwner->setName($json->ownerInfo->name);
            $placeOwner->setAddressline1($json->ownerInfo->addressline1);
            $placeOwner->setAddressline2($json->ownerInfo->addressline2);
            $placeOwner->setAddressline3($json->ownerInfo->addressline3);
            $placeOwner->setCity($json->ownerInfo->city);
            $placeOwner->setState($json->ownerInfo->state);
            $placeOwner->setCountry($json->ownerInfo->country);
            $placeOwner->setPostalcode($json->ownerInfo->postalcode);
            $placeOwner->setPhone($json->ownerInfo->phone);
            $placeOwner->setEmail($json->ownerInfo->email);
            $placeOwner->setPhonevalidated(true);
            $placeOwner->setEmailvalidated(false);

            $placeService->createUpdatePlace($place,$placeOwner);

            //$placeService->createUpdatePlaceOwner($placeOwner);

            $this->setSessionParm(WLConstants::S_PLACE_SLUG, $place->getSlug());

            $this->responseDetails->setMessage("Place " . $place->getName() . " created!");
            $this->responseDetails->addDetail("placeSlug",$place->getSlug());
           
            $response->setStatusCode(Response::HTTP_OK);
        }
        catch(Exception $ex){
            $this->logException($ex);
            $ex = BaseException::CREATE($ex);
            $this->responseDetails->setCode($ex->getCode());
            $this->responseDetails->setMessage($ex->getMessage());
            $this->responseDetails->addDetail("exception", $ex->__toString());
            $response->setStatusCode($ex->getResponseCode());
        } 
        $response->setContent(json_encode($this->responseDetails));
        return $response;
        
    }
  
}
