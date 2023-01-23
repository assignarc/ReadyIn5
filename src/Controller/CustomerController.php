<?php

namespace App\Controller;

use RI5\DB\Entity\Customer;
use RI5\DB\Entity\Data\WLConstants;
use RI5\Services\CustomerService;
use RI5\Services\OtpService;
use RI5\Services\PlaceService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;
use RI5\Exception\BaseException;

#[Route('/customer')]
class CustomerController extends BaseController
{
    #[Route('/reservations', name: 'CUST_Reservations' , methods: ["GET"])]
    public function getCustomerReservations(CustomerService $customerService): Response
    { 
        
       
        try{
            $this->checkCustomerPermissions([WLConstants::AUTHROLE_CUSTOMER]);
            return $this->render('customer/reservations.html.twig', [
                "phone" => $this->getSessionParm(WLConstants::S_CUST_PHONE,""),
                "customer"=> $customerService->findCustomer($this->getSessionParm(WLConstants::S_CUST_PHONE,"")) ?? new Customer(),
                "placeSlug" => $this->getSessionParm(WLConstants::S_CUST_PLACESLUG,""), 
                "menucontext" => "customer"
            ]);
        }
        catch(Exception $ex){
            $this->logException($ex);
            $ex = BaseException::CREATE($ex);
            return $this->render('Error/security.html.twig', [
                "exception" => $ex,
                "customer" => null,
                "placeSlug" => WLConstants::NONE,
                'menucontext' => WLConstants::NONE
            ]);
        }
       
        
    }

    #[Route('/profile', name: 'CUST_Profile')]
    public function customerProfile(CustomerService $customerService): Response
    { 
        try{
            $this->checkCustomerPermissions([WLConstants::AUTHROLE_CUSTOMER]);

            return $this->render('customer/profile.html.twig', [
                'phone' => $this->getSessionParm(WLConstants::S_CUST_PHONE,WLConstants::NONE),
                'placeSlug' => '',
                'customer'=> $customerService->findCustomer($this->getSessionParm(WLConstants::S_CUST_PHONE,WLConstants::NONE)) ?? new Customer(),
                'menucontext'=> "customer",
            ]);
        }
        catch(Exception $ex){
            $this->logException($ex);
            $ex = BaseException::CREATE($ex);
            return $this->render('Error/security.html.twig', [
                "exception" => $ex,
                "customer" => null,
                "placeSlug" => WLConstants::NONE,
                'menucontext' => WLConstants::NONE
            ]);
        } 
    }
    #[Route('/logout', name: 'CUST_Logout')]
    public function customerLogOut(): Response
    {
        $this->logout();
        $this->addFlash("success","User logged off!");

        return $this->redirect("/");
    }

    #[Route('/login', name: 'CUST_Login')]
    public function customerLogin(PlaceService $placeService): Response
    {

        try{
            return $this->render('customer/login.html.twig', [
                'phone' => $this->getSessionParm(WLConstants::S_CUST_PHONE,""),
                'placeSlug'=>$this->getSessionParm(WLConstants::S_CUST_PLACESLUG,""),
                'place'=> $placeService->findPlace($this->getSessionParm(WLConstants::S_CUST_PLACESLUG,"NONE")),
                'menucontext' => 'NONE'
            ]);
        }
        catch(Exception $ex){
            $this->logException($ex);
            $ex = BaseException::CREATE($ex);
            return $this->render('Error/security.html.twig', [
                "exception" => $ex,
                "customer" => null,
                "placeSlug" => WLConstants::NONE,
                'menucontext' => WLConstants::NONE
            ]);
        }
    }
   
  
}
