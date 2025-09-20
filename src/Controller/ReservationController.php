<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

#[Route('/reservation')]
class ReservationController extends BaseController
{
    // #[Route('/place/{placeSlug}/{queueSlug}', name: 'RESERVATION_Create' , methods: ["POST","GET"])]
    // public function resetaurantReservations(string $placeSlug = null, string $queueSlug = null,ReservationService $reservationSevice, PlaceService $placeService, CustomerService $customerService): Response
    // { 

    //     $this->setSessionParm(WLConstants::S_CUST_PLACESLUG,$placeSlug);
    //     $this->setSessionParm(WLConstants::S_PLACE_QUEUE,$queueSlug);
           

    //     $defaultData = ['message' => 'Reserve a Table'];
    //     $form = $this->createFormBuilder($defaultData)
    //                 ->add("adults", NumberType::class, ["label" => "Adults", "required"=>true])
    //                 ->add("children", NumberType::class,["label" => "Children", 'required'=>true])
    //                 ->add("notes", TextareaType::class,["label" => "Notes", 'required'=>false])
    //                 ->add("save", SubmitType::class,["label" => "Save"])
    //                 ->getForm();
        
    //     $form->handleRequest($this->request);
    //     $data = $form->getData();

    //     try{
    //         $reservation= new Reservation();
        
    //         if($this->getSessionParm(WLConstants::SESSION_AUTH_TOKEN,"") == "")
    //             throw new SecurityException();
    //         if($this->getSessionParm(WLConstants::SESSION_PHONE_TOKEN,"") =="") 
    //             throw new CustomerNotFoundException();  

    //         if($placeSlug != $this->getSessionParm(WLConstants::SESSION_REST_TOKEN,"") || 
    //                 $queueSlug != $this->getSessionParm(WLConstants::SESSION_REST_QUEUE_HASH,"")) 
    //             throw new PlaceNotFoundException();
            
    //         $customer = $customerService->findCustomer($this->getSessionParm(WLConstants::SESSION_PHONE_TOKEN,""));

    //         if(!$customer)
    //             throw new CustomerNotFoundException();  
            
           
    //         if ($form->isSubmitted() && $form->isValid()) {
    //             $reservation->setPlace($placeService->findPlace($this->getSessionParm(WLConstants::SESSION_REST_TOKEN, null)));
    //             $reservation->setQueueid($this->getSessionParm(WLConstants::SESSION_REST_QUEUE_HASH,""));
    //             $reservation->setAdults($data["adults"]);
    //             $reservation->setChildren($data["children"]);

    //             if($data["notes"]!=null && trim($data["notes"]) !="")
    //                 $reservation->setNotes(["CustomerNote"=> $data["notes"]]);
                
    //             $reservation->setReservationDt(new DateTime());
    //             $reservation->setCustomer($customer);
    //             $reservation->setStatus("WAIT");
    //             $reservation = $reservationSevice->createUpdateReservation($reservation);
                
    //             $this->addFlash("notice","Added to Reservation Queue successfully");
    //             return $this->customResponse($this->redirectToRoute("CUST_Reservations"));
    //         }

            
    //     }
    //     catch(Exception $ex){
    //         $this->logException($ex);
    //         $ex = BaseException::CREATE($ex);
    //         $this->addFlash("error", $ex->getMessage());
    //         $this->setSessionParm(WLConstants::SESSION_REDIRECT_URL,$this->request->getUri());
    //         return  $this->customResponse($this->redirectToRoute($ex->getRediretion(), 
    //                 array("route"=>"RESERVE_Place",
    //                         "placeSlug" => $placeSlug,
    //                         "queueSlug"=>$queueSlug), 
    //                 Response::HTTP_SEE_OTHER));
    //     }
    //     return $this->render('Reservation/new.html.twig', [
    //         'form' => $form,
    //         'reservation' => $reservation ??  new Reservation(),
    //         'phone' => $this->getSessionParm(WLConstants::SESSION_PHONE_TOKEN,""),
    //         'place'=> ($placeService->findPlace($this->getSessionParm(WLConstants::SESSION_REST_TOKEN,"")) ?? new Place()),
    //         'customer' => $customerService->findCustomer($this->getSessionParm(WLConstants::SESSION_PHONE_TOKEN,"")) ?? new Customer(),
    //         'placeHash' => $this->getSessionParm(WLConstants::SESSION_REST_TOKEN,""),
    //         'queueHash' => $this->getSessionParm(WLConstants::SESSION_REST_QUEUE_HASH,""),
    //     ]);
    // }
}
