<?php

namespace RI5\Services;

use RI5\DB\Entity\Customer;
use RI5\DB\Entity\Data\AuthToken;
use RI5\DB\Repository\CustomerRepository;
use RI5\DB\Repository\PlaceRepository;
use RI5\Services\Traits\EntityAwareTrait;

class AuthorizationService extends BaseService
{
    use EntityAwareTrait;
    protected PlaceRepository $placeRepository;
    protected CustomerRepository $customerRepository;

    public function __construct(PlaceRepository $placeRepository,CustomerRepository $customerRepository) 
    {   
        $this->placeRepository = $placeRepository; 
        $this->customerRepository = $customerRepository;
    }


    public function authorizeToken(AuthToken $token, bool $auth) : AuthToken{
        $token->setAuthorized($auth);
        return $token;
    }

    public function isCustomerAuthorized(string $tokenString ="", AuthToken $token = null, Customer $customer = null ):bool{
        if($token)
            return ($token->userId == $customer->getUserid()) && $token->isAuthorized();
        if($tokenString!=""){
            $token= AuthToken::toToken($tokenString);
            return ($token->userId == $customer->getUserid()) && $token->isAuthorized();
        }
        return false;
    }
  
}