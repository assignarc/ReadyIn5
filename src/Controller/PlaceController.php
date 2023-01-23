<?php

namespace App\Controller;

use RI5\DB\Entity\Data\WLConstants;
use RI5\DB\Entity\Place;
use RI5\Exception\BaseException;
use RI5\Services\CustomerService;
use RI5\Services\PlaceService;
use Exception;
use RI5\Exception\PlaceNotFoundException;
use RI5\Exception\SecurityException;
use RI5\Services\Traits\EncryptionTrait;
use RI5\Services\Traits\QrCodeTrait;
use RI5\Services\UrlService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/place')]
class PlaceController extends BaseController 
{

    use EncryptionTrait;
    use QrCodeTrait;

    #[Route('/{placeSlug}/reservations', name: 'PLACE_Reservations_Manage' , methods: ["GET"])]
    public function resetaurantReservations(string $placeSlug,PlaceService $placeService): Response
    { 
        try{
            $this->checkPlacePermissions([WLConstants::AUTHROLE_PLACE_OWNER, WLConstants::AUTHROLE_PLACE_MANAGER],$placeSlug);
            
            // if($placeSlug!=$this->getSessionParm(WLConstants::SESSION_REST_ADMIN_TOKEN, "NONE"))
            //         throw new SecurityException();

            //$this->setSessionParm(WLConstants::SESSION_REST_TOKEN,$placeSlug);
            
            return $this->render('place/reservations.html.twig', [
                "placeSlug" => $placeSlug,
                "place"=> $placeService->findPlace($placeSlug) ?? new Place(),
                "menucontext" => "place"
            ]);
        }
        catch(Exception $ex){
            $this->logException($ex);
            $ex = BaseException::CREATE($ex);
            return $this->render('Error/security.html.twig', [
                "placeSlug" => $placeSlug,
                "exception" => $ex,
                'menucontext' => 'NONE'
            ]);
        }
    }
    #[Route('/{placeSlug}/profile', name: 'PLACE_Profile_Update' , methods: ["GET"])]
    public function updatePlaceProfile(string $placeSlug, PlaceService $placeService): Response
    { 
        // $this->setSessionParm(WLConstants::SESSION_REST_ADMIN_ROLE,"admin");
        // $this->setSessionParm(WLConstants::SESSION_REST_ADMIN_TOKEN, $placeSlug);
       
        try{
            $this->checkPlacePermissions([WLConstants::AUTHROLE_PLACE_OWNER, WLConstants::AUTHROLE_PLACE_MANAGER],$placeSlug);
           

            // if($placeSlug!=$this->getSessionParm(WLConstants::SESSION_REST_ADMIN_TOKEN, "EMPTY-PLACE"))
            //     throw new SecurityException();
            
            return $this->render('Place/update.html.twig', [
                "placeSlug" => $placeSlug,
                "place" => $placeService->findPlace($placeSlug),
                "menucontext" => "place"
            ]);
        }
        catch(Exception $ex){
            $this->logException($ex);
            $ex = BaseException::CREATE($ex);
            return $this->render('Error/security.html.twig', [
                "placeSlug" => $placeSlug,
                "exception" => $ex,
                "place"=> $placeService->findPlace($placeSlug) ?? new Place(),
                "menucontext" => "place"
            ]);
        }
    }
    #[Route('/{placeSlug}/info', name: 'PLACE_Profile_Info' , methods: ["GET"])]
    public function getPlaceProfile(string $placeSlug, PlaceService $placeService): Response
    { 
        try{
            return $this->render('Place/info.html.twig', [
                "placeSlug" => $placeSlug,
                "place" => $placeService->findPlace($placeSlug),
                "menucontext" => "place"
            ]);
        }
        catch(Exception $ex){
            $this->logException($ex);
            $ex = BaseException::CREATE($ex);
            return $this->render('Error/security.html.twig', [
                "placeSlug" => $placeSlug,
                "exception" => $ex,
                "place"=> $placeService->findPlace($placeSlug) ?? new Place(),
                "menucontext" => "place"
            ]);
        }
    }
   
    #[Route('/{placeSlug}/reserve', name: 'PLACE_Reservation' , methods: ["GET"])]
    public function placeReservation(string $placeSlug,PlaceService $placeService, CustomerService $customerService): Response
    { 
        // $this->setSessionParm(WLConstants::SESSION_REST_ADMIN_ROLE,"admin");
        // $this->setSessionParm(WLConstants::SESSION_REST_ADMIN_TOKEN, $placeSlug);
        // $this->setSessionParm(WLConstants::SESSION_REST_TOKEN, $placeSlug);


        try{
            // if($this->getSessionParm(WLConstants::SESSION_REST_TOKEN, WLConstants::NONE) != $placeSlug)
            //     throw new PlaceInvalidRequestException("Invalid Place Link, please scan code to reserve.",4041);

            $this->checkPlacePermissions([WLConstants::AUTHROLE_PLACE_OWNER, WLConstants::AUTHROLE_PLACE_MANAGER],$placeSlug);
           
            $place = $placeService->findPlace($placeSlug);
            if(!$place)
                throw new PlaceNotFoundException("Invalid place or place not found!");

            $customer = $customerService->findCustomer($this->getSessionParm(WLConstants::S_PLACE_PHONE,WLConstants::NONE));
            
            
            return $this->render('place/reserve.html.twig', [
                'phone' => $this->getSessionParm(WLConstants::S_CUST_PHONE, WLConstants::NONE),
                'customer' => $customerService->findCustomer($this->getSessionParm(WLConstants::S_CUST_PHONE,WLConstants::NONE)),
                'placeSlug'=> $placeSlug,
                'place'=> $place,
                'menucontext'=>"place"
            ]);
        }
        catch(Exception $ex){
            $this->logException($ex);
            $ex = BaseException::CREATE($ex);
            return $this->render('Error/security.html.twig', [
                "placeSlug" => $placeSlug,
                "exception" => $ex,
                'menucontext' => 'NONE'
            ]);
        }
    }

    
    #[Route('/signup', name: 'PLACE_Profile_Signup' , methods: ["GET"])]
    public function placeSignup(): Response
    { 
       
        try{
            $this->logout();
            return $this->render('Place/signup.html.twig', [
                "placeSlug" => WLConstants::NONE,
                "menucontext"=>WLConstants::NONE
            ]);
        }
        catch(Exception $ex){
            $this->logException($ex);
            $ex = BaseException::CREATE($ex);
            return $this->render('Error/security.html.twig', [
                "placeSlug" => WLConstants::NONE,
                "menucontext"=>WLConstants::NONE,
                "exception" => $ex,
            ]);
        }
    }
     /**
     * Board URL Reservation
     */
    #[Route('/{placeSlug}/{urlSlug}/check', name: 'PLACE_AuthReservation' , methods: ["GET"])]
    public function placeAuthReservation(string $placeSlug,string $urlSlug): Response
    { 
        try{
            //Key|timeUrlCreated|timeUrlCreated
            $slugParts = explode("|",  EncryptionTrait::Decode($urlSlug, $placeSlug));

            if($slugParts[1] != $slugParts[2])
                throw new SecurityException("Invalid link, please check again!",4034);

            $url = "/place/" . $placeSlug . "/reserve";

            $this->setSessionParm(WLConstants::S_CUST_PLACESLUG,"{$placeSlug}");
            $this->setSessionParm(WLConstants::S_CUST_ROLE,WLConstants::AUTHROLE_CUSTOMER);
            //$this->setSessionParm(WLConstants::S_CUST_AUTHORIZED,WLConstants::AUTH_UNAUTHORIZED);

            return $this->redirect($url,302);
        }
        catch(Exception $ex){
            $this->logException($ex);
            $ex = BaseException::CREATE($ex);
            return $this->render('Error/security.html.twig', [
                "placeSlug" => $placeSlug,
                "exception" => $ex,
                'menucontext' => 'NONE'
            ]);
        }
    }
   
    #[Route('/{placeSlug}/{urlSlug}/public', name: 'PLACE_Public_Dashboard' , methods: ["GET"])]
    public function placeDashboard(string $placeSlug,string $urlSlug,PlaceService $placeService, UrlGeneratorInterface $router): Response
    { 
       
        try{
            //Key|PlaceSlug|timeUrlCreated
            $slugParts = explode("|",  EncryptionTrait::Decode($urlSlug, $placeSlug));
            //Key|timeUrlCreated|timeUrlCreated
            $url = $router->generate("WL_Home",[], UrlGeneratorInterface::ABSOLUTE_URL) . "place/" . $placeSlug . "/" .  EncryptionTrait::Encode("{$slugParts[0]}|{$slugParts[2]}|{$slugParts[2]}", $placeSlug) . "/check";
            $qrCode = $this->gnerateQrCode($url,null,true);
            $this->setSessionParm(WLConstants::S_PLACE_PUBLIC,$placeSlug);
            
            return $this->render('place/public.board.html.twig', [
                "placeSlug" => $placeSlug,
                "menucontext"=>"place",
                "qrcode" => $qrCode,
                "url" => $url,
                "place" => $placeService->findPlace($placeSlug,true)
            ]);
        }
        catch(Exception $ex){
            $this->logException($ex);
            $ex = BaseException::CREATE($ex);
            return $this->render('Error/security.html.twig', [
                "placeSlug" => $placeSlug,
                "exception" => $ex,
                "menucontext" => 'NONE',
              
            ]);
        }
    }
   
    #[Route('/{placeSlug}/board', name: 'PLACE_Board' , methods: ["GET"])]
    public function placeBoard(string $placeSlug,PlaceService $placeService, UrlService $urlService): Response
    { 
       
        try{
            $this->checkPlacePermissions([WLConstants::AUTHROLE_PLACE_OWNER, WLConstants::AUTHROLE_PLACE_MANAGER],$placeSlug);
           
            $this->setSessionParm(WLConstants::S_PLACE_SLUG,$placeSlug);
           
            $place = $placeService->findPlace($placeSlug,true);
            $time = time();
            $key = $urlService->createUpdateUrl(WLConstants::ENTITY_PLACE,$place->getPlaceid(), 
                                                "/place/".$placeSlug . "/reserve", 
                                                ["placeName"=> $place->getName()],
                                                false);
            
            //Key|PlaceSlug|timeUrlCreated
            $url = "/place/" . $placeSlug . "/" . EncryptionTrait::Encode("{$key}|{$placeSlug}|{$time}", $placeSlug) . "/public";
           
            return $this->redirect($url, 302);
            
        }
        catch(Exception $ex){
            $this->logException($ex);
            $ex = BaseException::CREATE($ex);
            return $this->render('Error/security.html.twig', [
                "placeSlug" => $placeSlug,
                "exception" => $ex,
                "menucontext" => 'NONE'
            ]);
        }
    }

    #[Route('/{placeSlug}/plan', name: 'PLACE_Plan' , methods: ["GET"])]
    public function plan(string $placeSlug,PlaceService $placeService): Response
    { 
        try{
            $this->checkPlacePermissions([WLConstants::AUTHROLE_PLACE_OWNER, WLConstants::AUTHROLE_PLACE_MANAGER],$placeSlug);
           
            $this->setSessionParm(WLConstants::S_PLACE_SLUG,$placeSlug);
           
            $place = $placeService->findPlace($placeSlug,true);
          
            return $this->render('Place/payplan.html.twig', [
                "placeSlug" => $placeSlug,
                "place" => $placeService->findPlace($placeSlug),
                "menucontext" => "place"
            ]);
        }
        catch(Exception $ex){
            $this->logException($ex);
            $ex = BaseException::CREATE($ex);
            return $this->render('Error/security.html.twig', [
                "placeSlug" => $placeSlug,
                "exception" => $ex,
                "menucontext" => 'NONE'
            ]);
        }
    }
    
    #[Route('/manager', name: 'PLACE_Manager')]
    public function placeManager(PlaceService $placeService): Response
    {
       
        try{
            $this->checkPlacePermissions([WLConstants::AUTHROLE_PLACE_OWNER, WLConstants::AUTHROLE_PLACE_MANAGER]);
           
            
            return $this->render('place/manager.html.twig', [
                'phone' => $this->getSessionParm(WLConstants::S_PLACE_PHONE,WLConstants::NONE),
                'placeSlug'=>$this->getSessionParm(WLConstants::S_PLACE_SLUG,WLConstants::NONE),
                'owner'=> $placeService->getPlaceOwner($this->getSessionParm(WLConstants::S_PLACE_PHONE,WLConstants::NONE)),
                "menucontext" => "place",
                "place" => null

            ]);
        }
        catch(Exception $ex){
            $this->logException($ex);
            $ex = BaseException::CREATE($ex);
            return $this->render('Error/security.html.twig', [
                "exception" => $ex,
                "placeSlug" => WLConstants::NONE,
                'menucontext' => WLConstants::NONE
            ]);
        }
       

      
    }
    #[Route('/{placeSlug}/login', name: 'PLACE_Specific_Login')]
    #[Route('/login', name: 'PLACE_Login')]
    public function placeLogin(PlaceService $placeService, string $placeSlug=null): Response
    {
        if($placeSlug)
            $this->setSessionParm(WLConstants::S_PLACE_SLUG,$placeSlug);
        else 
            $this->setSessionParm(WLConstants::S_PLACE_SLUG,WLConstants::NONE);
       
       return $this->render('place/login.html.twig', [
            'phone' => $this->getSessionParm(WLConstants::S_PLACE_PHONE,""),
            'placeSlug'=>$this->getSessionParm(WLConstants::S_PLACE_SLUG,""),
            'place'=> $placeService->findPlace($this->getSessionParm(WLConstants::S_PLACE_SLUG,"NONE")),
            'menucontext' => 'NONE'
        ]);
       
    }
    #[Route('/{placeSlug}/logout', name: 'PLACE_Specific_Logout')]
    #[Route('/logout', name: 'PLACE_Logout')]
    public function placeLogOut(): Response
    {
        $this->logout();
        $this->addFlash("success","User logged off!");
        return $this->redirect("/");
    }
}
