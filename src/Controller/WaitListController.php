<?php

namespace App\Controller;

use RI5\Services\CustomerService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class WaitListController extends BaseController
{
    #[Route('/', name: 'WL_Home' , methods: ["GET"])]
    public function customerReservations(CustomerService $customerService): Response
    { 
        return $this->render('home.html.twig', [
            
            // "phone" => $this->getSessionParm(WLConstants::SESSION_PHONE_TOKEN,""),
            // "customer"=> $customerService->findCustomer($this->getSessionParm(WLConstants::SESSION_PHONE_TOKEN,"")) ?? new Customer(),
            // "placeSlug" => $this->getSessionParm(WLConstants::SESSION_REST_TOKEN,""), 
            // "menucontext" => "customer"
        ]);
    }
}
