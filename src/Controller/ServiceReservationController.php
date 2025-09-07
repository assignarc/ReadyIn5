<?php

namespace App\Controller;

use RI5\DB\Entity\Data\ReservationStatus;
use RI5\DB\Entity\Data\WLConstants;
use RI5\Exception\BaseException;
use RI5\Exception\SecurityException;
use RI5\Services\ReservationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;
use RI5\Exception\ReservationStatusException;

#[Route('/svc')]
class ServiceReservationController extends BaseController 
{
   
    #[Route('/reservation/{reservationId}/status', name: 'RES_API_StatusGet' , methods: ["GET"])]
    public function getReservationStatus(string $reservationId, ReservationService $reservationService): Response
    { 
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        try{
            $this->checkCustomerPermissions([WLConstants::AUTHROLE_CUSTOMER], "","");

            $status = $reservationService->getReservationStatus($reservationId);
            $this->responseDetails->setMessage("Success! Reservation found - " . $status->value);
            $this->responseDetails->addDetail("status", $status->value);

            $placeStatus = $reservationService->getReservationWaitPlan($reservationId);
            foreach($placeStatus as $key=>$value){
                $this->responseDetails->addDetail($key,$value);
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
    #[Route('/reservation/cancel', name: 'RES_API_Cancel' , methods: ["POST"])]
    public function updateReservationStatus(ReservationService $reservationService): Response
    { 
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        try{
            $this->checkCustomerPermissions([WLConstants::AUTHROLE_CUSTOMER], "","");
            
            if($this->postParm("loginType","")!="customer"){
                throw new ReservationStatusException("Reservation can not be cancelled at this time, incorrect status found!");
            }
            $reservationId = $this->postParm("reservationId",null);
            $reservation = $reservationService->updateReservationStatus($reservationId, ReservationStatus::from("CANCEL"));
            $this->responseDetails->setMessage("Success! Reservation set to " .  "CANCEL");
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
