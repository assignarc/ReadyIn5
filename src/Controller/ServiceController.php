<?php

namespace App\Controller;

use Exception;
use RI5\DB\Entity\Data\WLConstants;
use RI5\Exception\BaseException;
use RI5\Exception\InvalidRequestException;
use RI5\Exception\SecurityException;
use RI5\Services\CustomerService;
use RI5\Services\OtpService;
use RI5\Services\PlaceService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//https://ourcodeworld.com/articles/read/1386/how-to-generate-the-entities-from-a-database-and-create-the-crud-automatically-in-symfony-5

#[Route('/svc')]
class ServiceController extends BaseController 
{
   
    #[Route('/customer/otp', name: 'OTP_API_Customer' , methods: ["POST"])]
    public function getCustomerOtp(OtpService $otpService, CustomerService $customerService, PlaceService $placeService): Response
    { 
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        try{
             switch ($this->postParm("type", "")) {
                case 'sendcode':
                    $this->setSessionParm(WLConstants::S_CUST_PHONE,($this->postParm("phone", WLConstants::NONE)));
                    $this->setSessionParm(WLConstants::S_CUST_CONTACT_METHOD,($this->postParm("method", "sms")));
                    $this->setSessionParm(WLConstants::S_AUTH_TYPE,($this->postParm("logintype", "customer")));
                    $this->setSessionParm(WLConstants::S_REDIRECT_URL,($this->postParm("redirectUrl", WLConstants::NONE)));

                    $customerService->setContactMethod(
                                    $this->getSessionParm(WLConstants::S_CUST_PHONE,WLConstants::NONE), 
                                    $this->getSessionParm(WLConstants::S_CUST_CONTACT_METHOD,null));

                    $key = $otpService->createOtp(
                                    $this->getSessionParm(WLConstants::S_CUST_PHONE,WLConstants::NONE), 
                                    array_merge([], $this->getSessionAllParms()), 
                                    $this->getSessionParm(WLConstants::S_CUST_CONTACT_METHOD,"sms"),
                                    true);

                    $this->setSessionParm(WLConstants::S_CUST_AUTHORIZED,WLConstants::AUTH_UNAUTHORIZED);

                    $this->logDebug("OTP Key Added: Phone - {$this->postParm("phone", "")}; Key - {$key} ");
                    $this->setSuccessResponse("OTP Sent successfully!",0,[]);

                    break;
                case 'verify':
                    $otpService->validateOtp($this->postParm("phone", WLConstants::NONE),$this->postParm("otp", WLConstants::NONE));
                    $customer = $customerService->findCustomer($this->getSessionParm(WLConstants::S_CUST_PHONE,""));
                    if($customer){
                        $this->setSessionParm(WLConstants::S_CUST_AUTHORIZED,WLConstants::AUTH_AUTHORIZED);
                        $this->setSessionParm(WLConstants::S_CUST_PHONE,$this->postParm("phone", WLConstants::NONE));
                        $this->setSessionParm(WLConstants::S_CUST_ROLE, WLConstants::AUTHROLE_CUSTOMER);
                        $this->setSessionParm(WLConstants::S_AUTH_TYPE,WLConstants::AUTHTYPE_CUSTOMER);

                        $response->headers->set("token",$this->createSecurityToken());
                        $this->setSuccessResponse("Passcode validated, " .  $this->getSessionParm(WLConstants::S_CUST_PHONE," ") . " verified.",0,
                            ['customer' => $customer]
                        );
                    }
                    else 
                        throw new SecurityException(message: "Access denied!");
                    break;
                default:
                    $this->setSessionParm(WLConstants::S_CUST_AUTHORIZED,WLConstants::AUTH_UNAUTHORIZED);
                    $this->setSessionParm(WLConstants::S_CUST_PHONE,WLConstants::NONE);
                    $this->setSessionParm(WLConstants::S_CUST_ROLE, WLConstants::NONE);        
                    throw new InvalidRequestException("Invalid OTP request.", 0,[],null);
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

    
    #[Route('/place/otp', name: 'OTP_API_Place' , methods: ["POST"])]
    public function getPlaceOtp(OtpService $otpService, CustomerService $customerService, PlaceService $placeService): Response
    { 
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        try{
             switch ($this->postParm("type", "")) {
                case 'sendcode':
                    $this->setSessionParm(WLConstants::S_PLACE_PHONE,($this->postParm("phone", WLConstants::NONE)));
                    $this->setSessionParm(WLConstants::S_CUST_CONTACT_METHOD,($this->postParm("method", "sms")));
                    $this->setSessionParm(WLConstants::S_AUTH_TYPE,($this->postParm("logintype", "customer")));
                    $this->setSessionParm(WLConstants::S_REDIRECT_URL,($this->postParm("redirectUrl", WLConstants::NONE)));

                    $key = $otpService->createOtp(
                                        $this->getSessionParm(WLConstants::S_PLACE_PHONE,WLConstants::NONE), 
                                        array_merge([], $this->getSessionAllParms()), 
                                        $this->getSessionParm(WLConstants::S_CUST_CONTACT_METHOD,"sms"),
                                        true);
                    $this->setSessionParm(WLConstants::S_PLACE_AUTHORIZED,WLConstants::AUTH_UNAUTHORIZED);
                   
                    $this->logDebug("OTP Key Added: Phone - {$this->postParm("phone", "")}; Key - {$key} ");
                    $this->setSuccessResponse("OTP Sent successfully!",0,[]);

                    break;
                case 'verify':
                    $otpService->validateOtp($this->postParm("phone", WLConstants::NONE),$this->postParm("otp", WLConstants::NONE));

                    $owner = $placeService->getPlaceOwner($this->postParm("phone",""));
                    if(!$owner){
                        throw new SecurityException("User not found or invalid OTP. Access denited");
                    }
                    
                    $places = $placeService->findPlacesByOwner($owner);

                    if($places){
                        
                        $this->setSessionParm(WLConstants::S_PLACE_AUTHORIZED,WLConstants::AUTH_AUTHORIZED);
                        $this->setSessionParm(WLConstants::S_PLACE_PHONE,$this->postParm("phone", WLConstants::NONE));
                        $this->setSessionParm(WLConstants::S_PLACE_ROLE,WLConstants::AUTHROLE_PLACE_OWNER);
                        $this->setSessionParm(WLConstants::S_AUTH_TYPE,WLConstants::AUTHTYPE_PLACE);
                        $slugString="";
                        foreach ($places as $place) {
                            $slugString = $slugString .",{$place->getSlug()}";
                        }
                        $this->setSessionParm(WLConstants::S_PLACE_PLACESLUGS,$slugString);
                        $response->headers->set("token",$this->createSecurityToken());
                        $this->setSuccessResponse("passcode validated" .  $this->getSessionParm(WLConstants::S_PLACE_PHONE," ") . " verified." ,0,[]);
                     }
                    else
                        throw new SecurityException("Access denied!");     
                        
                    break;
                default:
                    $this->setSessionParm(WLConstants::S_PLACE_AUTHORIZED,WLConstants::AUTH_UNAUTHORIZED);
                    $this->setSessionParm(WLConstants::S_PLACE_PHONE,WLConstants::NONE);
                    $this->setSessionParm(WLConstants::S_PLACE_ROLE,WLConstants::NONE);
                   
                    throw new InvalidRequestException("Invarlid OTP request.", 0,[],null);
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
}
