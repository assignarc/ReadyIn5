<?php

namespace App\Controller;

use RI5\DB\Entity\Data\ReservationStatus;
use RI5\DB\Entity\Data\WLConstants;
use RI5\DB\Entity\PlaceUser;
use RI5\DB\Entity\Reservation;
use RI5\DB\Entity\Place;
use RI5\DB\Entity\PlaceHolidays;
use RI5\DB\Entity\PlaceOwner;
use RI5\DB\Entity\PlaceSchedule;
use RI5\Exception\BaseException;
use RI5\Exception\InvalidRequestException;
use RI5\Exception\PlaceNotFoundException;
use RI5\Exception\SecurityException;
use RI5\Services\CustomerService;
use RI5\Services\ReservationService;
use RI5\Services\PlaceService;
use DateTime;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;
use RI5\DB\Entity\PlaceQueue;
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
            $this->setSuccessResponse("Place found",0,
                        ['place' => $place]
            );
        }
        catch(Exception $ex){
            $ex = BaseException::CREATE($ex);
            $this->setExceptionResponse($ex);
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
            $this->setSuccessResponse("Current reservations loaded.",0,
                ['reservations' => $reservations]
            );
            $response->setStatusCode(Response::HTTP_OK);
        }
        catch(Exception $ex){
            $ex = BaseException::CREATE($ex);
            $this->setExceptionResponse($ex);
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
            $this->setSuccessResponse("Success! Reservation set to " .  $status,0,
                ['reservation' => $reservation]
            );
            $response->setStatusCode(Response::HTTP_OK);
        }
        catch(Exception $ex){
            $ex = BaseException::CREATE($ex);
            $this->setExceptionResponse($ex);
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
                throw new InvalidRequestException("Incorrect User or Place.", 9503 ,  [],null);

            //The Qid is AUTO INCREMENT, that will take it beyond 2 digits, so commenting it out. 
            //$reservation->setQueueid($this->postParm("queueid",$placeService->getDefaultQueueId($place)));  
            $reservation = new Reservation()
                ->setAdults($this->postParm("adults",1))
                ->setChildren($this->postParm("children",0))
                ->setInstructions($this->postParm("specialnotes",null))
                ->setCustomer($customer)
                ->setPlace($place)
                ->setStatus(ReservationStatus::STATUS_WAIT->value)
                ->setReservationDt(new DateTime())
                ->setQueueid(1);
            
 
            $this->setSuccessResponse("Success! Place reserved at " . $place->getName(),0,
                        ['reservation' => $reservationService->createReservation($reservation,false)]
            );   
            $response->setStatusCode(Response::HTTP_OK);
        }
        catch(Exception $ex){
            $ex = BaseException::CREATE($ex);
            $this->setExceptionResponse($ex);
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

            $place = $placeService->findPlace($placeSlug, false);
            
            if(!$place){
                throw new InvalidRequestException("Place not found or you are not authorized to manage it",9311, [], null);
            }
            switch ($reqType) {
                case "queues":
                    $this->setSuccessResponse("Place queues found.",0,
                        [$reqType => $place->getPlaceQueues()]);
                case "holidays":
                case "users":
                case "schedules":
                case "images":
                    $this->setSuccessResponse($reqType . " found.",0,
                        [$reqType => $placeService->getPlaceMeta($placeSlug,$reqType)]);
                    break;
                case "meta":
                    $this->setSuccessResponse("Place found.",0,
                        ['place' => $place]);
                    break;
                default:
                    throw new InvalidRequestException("Invalid Place request.", 9321, [], null);

            }
            $response->setStatusCode(Response::HTTP_OK);
        }
        catch(Exception $ex){
            $ex = BaseException::CREATE($ex);
            $this->setExceptionResponse($ex);
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

            $this->setSuccessResponse("Place owner profile found",0,
                        ['placeOwner' => $placeOwner]
            );  
        }
        catch(Exception $ex){
            $ex = BaseException::CREATE($ex);
            $this->setExceptionResponse($ex);
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
          
            $this->setSuccessResponse("Place owner profile found",0,
                ['places' => $places,
                    'slugString' => $this->getSessionParm(WLConstants::S_PLACE_PLACESLUGS,WLConstants::NONE)
                ]
            ); 
        }
        catch(Exception $ex){
            $ex = BaseException::CREATE($ex);
            $this->setExceptionResponse($ex);
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
           
            switch($this->request->getMethod()) {
                case 'POST':
                    switch ($reqType) {
                        case 'schedules':
                             // $json = json_decode($this->request->getContent());
                            $placeSchedule = new PlaceSchedule()
                                ->setPlaceid($this->postParm("placeid",""))
                                ->setOpenTime($this->postParm("openTime",""))
                                ->setCloseTime($this->postParm("closeTime",""))
                                ->setShift($this->postParm("shift","default"))
                                ->setDay($this->postParm("day",""));

                            $placeService->createUpdatePlaceSchedule($placeSchedule,scheduleid: $this->postParm("scheduleid",null));
                            $this->setSuccessResponse("Schedule change request completed.",0,
                                ['schedule' => $place->getPlaceSchedules()]
                            );
                            break;
                        
                        case 'holidays':
                            $placeHoliday = new PlaceHolidays()
                                ->setPlace($place)
                                ->setPlaceId($place->getPlaceId())
                                ->setHolidayDate(new DateTime($this->postParm("holidayDate","")))
                                ->setHolidayName($this->postParm("holidayName",""))
                                ->setSpecialNote($this->postParm("specialNote",""));

                            $placeService->createUpdatePlaceHolidays($placeHoliday, $this->postParm("holidayid",null));
                            $this->setSuccessResponse("Holiday change request completed.",0,
                                ['holidays' => $place->getPlaceHolidays()]
                            );
                            break;

                        case 'users':
                            $this->logMessageArray(["accessreservation",$this->postParm("accessreservation","false")]);
                            $placeUser = new PlaceUser()
                                ->setPlace($place)
                                ->setPlaceid($place->getPlaceid())
                                ->setUsername($this->postParm("username",""))
                                ->setAccessreservation($this->postParm("accessreservation","false"))
                                ->setPhone($this->postParm("phone",""))
                                ->setAccessadmin($this->postParm("accessadmin","false"));

                            $placeService->createUpdatePlaceUser($placeUser, $this->postParm("placeuserid",null));
                                
                            $this->setSuccessResponse("User change request completed.",0,
                                ['users' => $place->getPlaceUsers()]
                            );
                            break;

                        case 'owner':
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
                            $this->setSuccessResponse("Owner change request completed.",0,
                                ['owner' => $placeOwner]
                            );
                            break;

                        case 'queues':
                           $qname = $this->postParm("queuename","") ;
                           $qname = $qname == "" ? $placeService->generateDefaultQueueName($this->postParm("capcityTotal",1),
                                                                                            $this->postParm("capacityAdults",1),
                                                                                            $this->postParm("capacityChildren",0),
                                                                                            $place->getPlaceid())
                                                : $qname;
                    
                            $queue = new PlaceQueue()
                                        ->setPlaceid($place->getPlaceid())
                                        ->setCapacityAdults($this->postParm("capacityAdults",1))
                                        ->setCapacityChildren($this->postParm("capacityChildren",0))
                                        ->setCapcityTotal($this->postParm("capcityTotal",1))
                                        ->setQueuename($qname);
                                        
                            $placeService->createUpdateQueue($queue,$this->postParm("queueid",null));
                            $this->setSuccessResponse("Queue change request completed!",0,
                                ['queues' => $place->getPlaceQueues()]
                            );
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
                            
                            $place = $placeService->createUpdatePlace($place,null);
                            $this->setSuccessResponse("Place update request completed.",0,
                                ['place' => $place]
                            );
                            break;

                        default:
                            throw new InvalidRequestException("Invalid Request", 9321, []);
                    }
                    break;

                case 'DELETE':
                    switch ($reqType) {
                        case 'holidays':
                            $placeService->removeMetaEntity("holidays",$this->postParm("holidayid",""));
                            $this->setSuccessResponse("Holiday removed!",0,
                                ['holidays' => $place->getPlaceHolidays()]
                            );
                            break;
                        case 'queues':
                            $placeService->removeMetaEntity($reqType,$this->postParm("queueid",""));
                            $this->setSuccessResponse("Queue removed! ",0,
                                ['queues' => $place->getPlaceQueues()]
                            );
                            break;
                        default:
                            throw new InvalidRequestException("Invalid Request", 9321, [], null);
                    }
                    break;

                default:
                    throw new InvalidRequestException("Invalid Request", 9323, [], null);
            }  
            $response->setStatusCode(Response::HTTP_OK);
        }
        catch(Exception $ex){
            $ex = BaseException::CREATE($ex);
            $this->setExceptionResponse($ex);
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
            $this->setSuccessResponse("Place " . $place->getName() . " created!",0,
                        ['place' => $place,
                         'placeSlug' => $place->getSlug()
                        ]
            );
       
            $response->setStatusCode(Response::HTTP_OK);
        }
        catch(Exception $ex){
            $ex = BaseException::CREATE($ex);
            $this->setExceptionResponse($ex);
            $response->setStatusCode($ex->getResponseCode());
        } 
        $response->setContent(json_encode($this->responseDetails));
        return $response;
        
    }
  
}
