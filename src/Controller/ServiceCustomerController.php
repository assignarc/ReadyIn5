<?php

namespace App\Controller;

use RI5\Exception\BaseException;
use RI5\Services\CustomerService;
use RI5\Services\ReservationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;
use RI5\DB\Entity\Customer;
use RI5\DB\Entity\Data\ReservationStatus;
use RI5\DB\Entity\Data\WLConstants;
use RI5\DB\Events\DataNormalizationNeeded;
use RI5\DB\Events\TimeUp;
use RI5\Services\Traits\EventDispatcherTrait;

#[Route('/svc')]
class ServiceCustomerController extends BaseController
{

    use EventDispatcherTrait;

    #[Route('/customer/profile', name: 'CUST_API_ProfileCreate' , methods: ["POST"])]
    public function updateCustomerProfile(CustomerService $customerService): Response
    { 
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
      
     
        try{
           
            $this->checkCustomerPermissions([WLConstants::AUTHROLE_CUSTOMER], "", $this->postParm("phone",null));

            $customerDB = $customerService->findCustomer($this->postParm("phone","0"));
            
            if(!$customerDB){
                $customerDB= new Customer();
                $customerDB->setPhone($this->postParm("phone",null));
            }
            $customerDB->setFirstname($this->postParm("firstname",null));
            $customerDB->setLastname($this->postParm("lastname",null));
            $customerDB->setContactMethod($this->postParm("contactMethod","sms"));

            if($this->postParm("full","0")=="1"){
                $customerDB->setAddressline1($this->postParm("addressline1",null));
                $customerDB->setAddressline2($this->postParm("addressline2",null));
                $customerDB->setAddressline3($this->postParm("addressline3",null));
                $customerDB->setCity($this->postParm("city",null));
                $customerDB->setState($this->postParm("state",null));
                $customerDB->setCountry($this->postParm("country",null));
                $customerDB->setPostalcode($this->postParm("postalcode",null));
            }
            
            $customerService->createUpdateCustomer($customerDB);
            $this->setSuccessResponse("Customer updated",0,
                ['customer' => $customerDB]
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
    #[Route('/customer/profile', name: 'CUST_API_ProfileGet' , methods: ["GET"])]
    #[Route('/customer/info', name: 'CUST_API_InfoGet' , methods: ["GET"])]
    public function getustomerProfile(CustomerService $customerService): Response
    { 
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        try{
            // if($this->getSessionParm(WLConstants::SESSION_AUTH_TOKEN,"0")!="1"){
            //     throw new SecurityException("User unauthorized");
            // }
            // if($this->getSessionParm(WLConstants::SESSION_PHONE_TOKEN,"0")=="0"){
            //     throw new SecurityException("Unauthorized request");
            // }

            $this->checkCustomerPermissions([WLConstants::AUTHROLE_CUSTOMER], "", "");
            $customerDB = $customerService->findCustomer($this->getSessionParm(WLConstants::S_CUST_PHONE,"0"));
            $this->setSuccessResponse("Customer found",0,
                ['customer' => $customerDB]
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
    #[Route('/customer/reservations/current', name: 'CUST_API_ReservationCurrentGet' , methods: ["GET"])]
    public function currentCustomerReservations(CustomerService $customerService, ReservationService $reservationService): Response
    { 

        
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        
        try{
            // if($this->getSessionParm(WLConstants::SESSION_AUTH_TOKEN,"0")!="1"){
            //     throw new SecurityException("User unauthorized");
            // }
            $this->checkCustomerPermissions([WLConstants::AUTHROLE_CUSTOMER], "", );

            $inResStatuses=[
                    ReservationStatus::STATUS_CALL,
                    ReservationStatus::STATUS_WAIT,
                    ReservationStatus::STATUS_CANCEL,
                    ReservationStatus::STATUS_SERVE,
                    ReservationStatus::STATUS_EXPIRED,
                    ReservationStatus::STATUS_NOSHOW,
                    ReservationStatus::STATUS_COMPLETE
                ];
            $customer = $customerService->findCustomer($this->getSessionParm(WLConstants::S_CUST_PHONE,""));
            $reservations = $reservationService->findAllByCustomerId($customer->getUserid(), null,0,100, $inResStatuses,[]);
            $this->setSuccessResponse("Current reservations loaded.",0,
                ['reservations' => $reservations]
            );
          

            //TO-DO send a cleanup event. Remove when the Code is implemented for Cron or Scheduler. 
            $this->dispatchEvent(new TimeUp(),TimeUp::NAME);
            $this->dispatchEvent(new DataNormalizationNeeded("RESERVATION_NORMALIZATION"), DataNormalizationNeeded::NAME);
        }
        catch(Exception $ex){
            $ex = BaseException::CREATE($ex);
            $this->setExceptionResponse($ex);
            $response->setStatusCode($ex->getResponseCode());
        } 
        $response->setContent(json_encode($this->responseDetails));
        return $response;
    }
    
    #[Route('/customer/reservations/past', name: 'CUST_API_ReservationPastGet',methods: ["GET"])]
    public function pastCustomerReservations(CustomerService $customerService, ReservationService $reservationService): Response
    { 
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        try{
            // if($this->getSessionParm(WLConstants::SESSION_AUTH_TOKEN,"0")!="1"){ 
            //     throw new SecurityException("User unauthorized");
            // }
            $this->checkCustomerPermissions([WLConstants::AUTHROLE_CUSTOMER], placeSlug: "", phone: "");
            $customer = $customerService->findCustomer($this->getSessionParm(WLConstants::S_CUST_PHONE,""));
            $reservations = $reservationService->findAllByCustomerId($customer->getUserid(), 
                            (new \DateTime())->modify('-1 day'));//new DateTime());

            $this->setSuccessResponse("Past reservations loaded.",0,
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
/*
    #[Route('/1/{placeHash}/{queueHash}/1', name: 'API_Reserve_CustVerify', methods: ['POST'])]
    public function verifyCustomer(string $placeHash = null, string $queueHash = null, CustomerService $customerService): Response
    { 
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        
        try{
            if($placeHash != $this->getSessionParm(WLConstants::SESSION_REST_TOKEN,"") || $queueHash != $this->getSessionParm("queueHash","")) 
                throw new PlaceNotFoundException();
     
            $phoneNumber = $this->getParm("phone");

            $struct= new ReservationStruct();
            $struct->customer=$customerService->findCustomer($phoneNumber);

            $this->setSessionParm(WLConstants::SESSION_PHONE_TOKEN, $phoneNumber);
            $this->setSessionParm("authHash", "0");

            $response->setContent(json_encode($struct));
            $response->setStatusCode(Response::HTTP_OK);
        }
        catch(Exception $ex){
            $ex = BaseException::CREATE($ex);
            $response->setContent(json_encode($ex));
            $response->setStatusCode( $ex->getResponseCode());
        }
        return $response;

    }
    #[Route('/1/{placeHash}/{queueHash}/2', name: 'API_Reserve_CustCheck', methods: ['POST'])]
    public function lookupCustomer(string $placeHash = null, string $queueHash = null, CustomerService $customerService): Response
    { 
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        
        try{
            $struct = new ReservationStruct();

            if($placeHash != $this->getSessionParm("placeHash","") || $queueHash != $this->getSessionParm("queueHash","")) 
                throw new PlaceNotFoundException();
     
            $phoneNumber = $this->getParm("phone");
            $code = $this->getParm("code");

            if($code!="1234" || $this->getSessionParm("phoneHash","")!= $phoneNumber){
                $this->setSessionParm("authHash", "0");
                throw new SecurityException("Invalid verification code or Phone number, please try again");
            }
            else
                $this->setSessionParm("authHash", "1");

            $struct->customer=$customerService->findCustomer($phoneNumber);
            $response->setContent(json_encode($struct));
            $response->setStatusCode(Response::HTTP_OK);
        }
        catch(Exception $ex){
            $ex = BaseException::CREATE($ex);
            $response->setContent(json_encode($ex));
            $response->setStatusCode( $ex->getResponseCode());
        }
        return $response;

    }

    #[Route('/1/{placeHash}/{queueHash}/3', name: 'API_Reserve_CustEdit', methods: ['POST'])]
    public function updateCustomer(string $placeHash = null, string $queueHash = null, CustomerService $customerService,ReservationService $reservationSevice): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
       
        try{
            if($this->getSessionParm("authHash","") == 0)
                throw new SecurityException();
            if($this->getSessionParm("phoneHash","") == 0) 
                throw new CustomerNotFoundException();  
            
            if($placeHash != $this->getSessionParm("placeHash","") || $queueHash != $this->getSessionParm("queueHash","")) 
                throw new PlaceNotFoundException();
         
            $struct = new ReservationStruct();

            $customer = $customerService->buildCustomer(
                            $this->getParm("userid"), 
                            $this->getSessionParm("phoneHash",""), 
                            $this->getParm("firstname"), 
                            $this->getParm("lastname"));

            $struct->customer = $customerService->createUpdateCustomer($customer);
        
            $struct->reservation = $reservationSevice->findReservation(
                    $struct->customer->getUserid(), 
                    $placeHash, 
                    $this->getSessionParm("queueHash",""));

            $response->setStatusCode(Response::HTTP_OK);
            $response->setContent(json_encode($struct));
        }
        catch(Exception $ex){
            $ex = BaseException::CREATE($ex);
            $response->setContent(json_encode($ex));
            $response->setStatusCode( $ex->getResponseCode());
        }
        return $response;
    }
    #[Route('/1/{placeHash}/{queueHash}/4', name: 'API_Reserve', methods: ['POST'])]
    public function reserverTable(string $placeHash = null, string $queueHash = null, CustomerService $customerService, ReservationService $reservationSevice, PlaceService $placeService): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
       

        try{
            if($this->getSessionParm("authHash","") == 0)
                throw new SecurityException();
            if($this->getSessionParm("phoneHash","") == 0) 
                throw new CustomerNotFoundException();  

            if($placeHash != $this->getSessionParm("placeHash","") || $queueHash != $this->getSessionParm("queueHash","")) 
                throw new PlaceNotFoundException();
            
            $struct = new ReservationStruct();
            $customer = $customerService->findCustomer($this->getSessionParm("phoneHash",""));

            $reservation= new Reservation();
            $reservation->setPlaceid($placeService->findPlace($this->getSessionParm("placeHash", null))->getPlaceid());
            $reservation->setQueueid($this->getSessionParm("queueHash",""));
            $reservation->setAdults($this->getParm("adults"));
            $reservation->setChildren($this->getParm("children"));
            $reservation->setNotes(["note"=>"new"]);
            $reservation->setReservationDt(new DateTime());
            $reservation->setUserid($customer->getUserId());
            $reservation->setStatus("WAIT");
            $reservation = $reservationSevice->createUpdateReservation($reservation);
           
            $struct->customer = $customer;
            $struct->reservation =$reservation;
            $struct->message = "Added to Reservation Queue successfully";
          
            $response->setContent(json_encode($struct));
            $response->setStatusCode(Response::HTTP_OK);
            
        }
        catch(Exception $ex){
            $ex = BaseException::CREATE($ex);
            $response->setContent(json_encode($ex));
            $response->setStatusCode( $ex->getResponseCode());
        }
        
        return $response;
    }

    #[Route('/1/{placeHash}/{queueHash}', name: 'WL_Reserve_Begin')]
    public function PlaceCheck(string $placeHash = null, string $queueHash = null, PlaceService $placeService): Response
    { 
        $this->setSessionParm("placeHash", $placeHash);
        $this->setSessionParm("queueHash", $queueHash);

        $struct = new ReservationStruct();
        $struct->place = $placeService->findPlace($placeHash);
        return $this->render('Reserve/new.html.twig', [
            'struct' => $struct,
        ]);
    
    }

    #[Route('/1/{placeHash}/signup', name: 'WL_SignUp_CustomerCheck')]
    public function CustomerCheck(EntityManagerInterface $entityManager): Response
    {
        $step ="1";
        $customer = new Customer();
        $form = $this->createForm(SignUpType::class);

        $form->handleRequest($this->request);
        
        if ($form->get("checkContact")->isSubmitted() && $form->isValid()) {
            if($form->getClickedButton()->getName()=="checkContact"){
                $customer = $entityManager->getRepository(Customer::class)->findOneByPhone($form["phone"]->getData());
                 if(!$customer){
                    $customer = new Customer();
                    $customer->setPhone(strval($form["phone"]->getData()));
                    $customer->setFirstname("");
                    $customer->setLastname("");
                    $this->addFlashMessage('notice','Please create a new profile!');
                }
                else
                    $this->addFlashMessage('notice','Profile found!');
                
                $step=2;
            }
            else if($form->getClickedButton()->getName()=="saveContact"){
                
                $customer = $entityManager->getRepository(Customer::class)->findOneByPhone($form["phone"]->getData());
                if(!$customer){
                    $customer = new Customer();
                    $customer->setPhone($form["phone"]->getData());
                    $customer->setFirstname($form["firstName"]->getData());
                    $customer->setLastname($form["lastName"]->getData());
                    $entityManager->persist($customer);
                }
                else{
                    $customer->setFirstname($form["firstName"]->getData());
                    $customer->setLastname($form["lastName"]->getData());
                }
                $entityManager->flush();
                $this->session->set("customerPhone", $customer->getPhone());
                $this->addFlashMessage('notice','Your profile updated!');

                $customer = $entityManager
                    ->getRepository(Customer::class)
                    ->findOneByPhone($this->session->get("customerPhone","NONE"));

                $waitqueue = $entityManager
                    ->getRepository(Waitqueue::class)
                    ->findOneBy([
                        'userid' => $customer->getUserid(),
                        'placeid' => $this->session->get("placeHash","NONE"),
                        'queueid' => 0,
                    ]);
                $step="3";

                
            }
            else if($form->getClickedButton()->getName()=="requestTable"){
               
            }
        }
        else{     
            $customer->setPhone("");
            $customer->setFirstName("");
            $customer->setLastName("");
            $step="1";
         }

        return $this->render('signup/new.html.twig', [
            'customer' => $customer,
            'form' => $form,
            'place'=>  $this->session->get("placeHash","NONE"),
            'waitqueue' => $waitqueue,
            'step'=> $step ,
        ]);
        
    }

    */
}
