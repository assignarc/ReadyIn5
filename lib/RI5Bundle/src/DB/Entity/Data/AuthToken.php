<?php

namespace RI5\DB\Entity\Data;

use RI5\Services\Traits\EncryptionTrait;
use RI5\Services\Traits\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\Session\Session;

class AuthToken extends BaseObject
{
    // public string $S_AUTH_TYPE ="CUST"; //Place/Customer //SESSION_AUTH_TYPE
    // public string $S_PLACE_ROLE ="CUST"; //Customer/Owner/Manager //SESSION_REST_ADMIN_ROLE
    // public bool   $S_AUTHORIZED = false; //SESSION_AUTH_TOKEN

    // public string $S_PLACE_PLACESLUGS = ""; //SESSION_REST_ADMIN_TOKEN
    // public string $SESSION_CUSTOMER_ID= "";
    // public string $S_CUST_PHONE= ""; //SESSION_PHONE_TOKEN

  
    use EncryptionTrait;

    public mixed $AUTHDATA = [];

    public static string $TokenSignatureStart = "ri5:[*//";
    public static string $TokenSignatureEnd = "//*]";
    public string $userId = "";

    public function __construct(mixed $sessionData=null)
    {
        $this->AUTHDATA = $sessionData;
    }
    public function __toString() :string
    {
        return  (json_encode($this->AUTHDATA,true));
    }

    public function setAuthorized(mixed $auth=false){
        $this->AUTHDATA["S_AUTHORIZED"] = filter_var($auth,FILTER_VALIDATE_BOOLEAN);
    }
    public function isAuthorized(){
        return boolval($this->AUTHDATA["S_AUTHORIZED"]);
    }
   

    public static function toToken(string $tokenString) :self  {
        $tokenString = str_replace(AuthToken::$TokenSignatureStart,"", $tokenString);
        $tokenString = str_replace(AuthToken::$TokenSignatureEnd,"", $tokenString);
        $t =  json_decode(EncryptionTrait::Decode($tokenString),true);
        return AuthToken::Token($t);        

    }
 
    public static function fromToken(AuthToken $token) : string  {
        return  AuthToken::$TokenSignatureStart . EncryptionTrait::Encode($token->__toString()) . AuthToken::$TokenSignatureEnd;
    }


    public static function Token(array $session) : AuthToken {
        return new AuthToken($session);  
    }
}
