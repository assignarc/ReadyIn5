<?php

namespace App\Controller;

use RI5\DB\Entity\Data\WLConstants;
use RI5\Exception\OtpException;
use RI5\Services\OtpService;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//https://ourcodeworld.com/articles/read/1386/how-to-generate-the-entities-from-a-database-and-create-the-crud-automatically-in-symfony-5

#[Route('/')]
class DeeplinkController extends BaseController
{
  

    #[Route('q.{slug}', name: 'OTP_DeepLinkCheck')]
    public function otpLinkCheck(string $slug = null, OtpService $otpService): Response
    { 
        try{
            if(!$slug){
                throw new Exception("Invalid OTP link");
            }
            $otp = $otpService->validateOtpSlug($slug);
            if($otp && $otp->getPhone() !=""){
                $this->setSessionParm(WLConstants::S_CUST_PHONE,$otp->getPhone());
                $this->setSessionParm(WLConstants::S_CUST_AUTHORIZED,"1");
                $this->addFlash("success","OTP Validated, User logged in");
                if($otp->getNotes()){
                    if(array_key_exists(WLConstants::S_REDIRECT_URL, $otp->getNotes())){
                        if($this->routeExists($otp->getNotes()[WLConstants::S_REDIRECT_URL]))
                            return $this->customResponse($this->redirectToRoute($otp->getNotes()[WLConstants::S_REDIRECT_URL]));
                        else
                            return $this->customResponse($this->redirect($otp->getNotes()[WLConstants::S_REDIRECT_URL]));
                    }
                }
            }
            else{
                throw new OtpException("Invalid link or phone number");
            }
        }
        catch(Exception $ex){
            $this->logException($ex);
            $this->addFlash("error", $ex->getMessage());
            return $this->render('customer/otpslug.html.twig', [
                "placeSlug" => $this->setSessionParm(WLConstants::S_CUST_PLACESLUG,WLConstants::NONE),
                "exception" => $ex,
                "menucontext" => "customer",
                "phone" => $this->getSessionParm(WLConstants::S_CUST_PHONE,WLConstants::NONE),
            ]);
        }

        return $this->render('customer/otpslug.html.twig', [
            "placeSlug" => $this->setSessionParm(WLConstants::S_CUST_PLACESLUG,WLConstants::NONE),
            "menucontext" => "customer",
            "phone" => $this->getSessionParm(WLConstants::S_CUST_PHONE,WLConstants::NONE),
        ]);
    }

    // #[Route('/place/{slug}/t.{signupSlug}', name: 'OTP_DeepLinkCheck')]
    // public function signupLinkCheck(string $slug,string $signupSlug): Response
    // { 
    //     try{
    //         if(!$slug){
    //             throw new Exception("Invalid OTP link");
    //         }
            
    //         if("" !=""){
                
    //         }
    //         else{
    //             throw new OtpException("Invalid link or phone number");
    //         }
    //     }
    //     catch(Exception $ex){
    //         $this->logException($ex);
    //         $this->addFlash("error", $ex->getMessage());
    //         return $this->render('customer/otpslug.html.twig', [
    //             "placeSlug" => $this->setSessionParm(WLConstants::SESSION_REST_TOKEN,"1"),
    //             "exception" => $ex,
    //             "menucontext" => "customer",
    //             "phone" => $this->getSessionParm(WLConstants::SESSION_PHONE_TOKEN,""),
    //         ]);
    //     }

    //     return $this->render('customer/otpslug.html.twig', [
    //         "placeSlug" => $this->setSessionParm(WLConstants::SESSION_REST_TOKEN,"1"),
    //         "menucontext" => "customer",
    //         "phone" => $this->getSessionParm(WLConstants::SESSION_PHONE_TOKEN,""),
    //     ]);
    // }
}
