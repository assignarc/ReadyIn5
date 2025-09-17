<?php

namespace App\Controller;

use RI5\DB\Entity\Data\ResponseDetails;
use RI5\DB\Entity\Data\WLConstants;
use DateTimeInterface;
use Exception;
use RI5\DB\Entity\Data\AuthToken;
use RI5\Exception\BaseException;
use RI5\Exception\SecurityException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use RI5\Services\Traits\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
//https://ourcodeworld.com/articles/read/1386/how-to-generate-the-entities-from-a-database-and-create-the-crud-automatically-in-symfony-5


class BaseController extends AbstractController
{
  
    protected $session;
    protected $request;
    protected $cookies;
    protected $cookiesResponse =[];
    protected ResponseDetails $responseDetails;
    protected $parameters;
   
    use LoggerAwareTrait;
    // use CacheAwareTrait;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->session = $this->request->getSession();
        $this->cookies = $this->request->cookies;
        $this->responseDetails = new ResponseDetails();
    }
    /* Custom Response Details Functions */
    /**
     * Set the response details needed by UI to consume
     * @param string $message
     * @param int $code
     * @param mixed $details
     * @return void
     */
    public function setSuccessResponse(string $message = "Success", int $code = 0, mixed $details =[]){
        $this->responseDetails->set($message, $code, $details);  
    }
    public function setExceptionResponse(BaseException $exception){
        $this->logException($exception->getInnerException());
        $this->responseDetails->set($exception->getMessage(),$exception->getCode(),["exception"=>$exception->getMessage()]);
        $this->responseDetails->addDetail("exception", $exception->__toString());
    }
    /**
     * Add  a detail to the response details needed by UI to consume
     * @param string $key
     * @param mixed $detail
     * @return void
     */
    public function addResponseDetail(string $key, mixed $detail){
        $this->responseDetails->addDetail($key, $detail);
    }

    /*SYMFONY Specifics */
    protected function addFlashMessage(string $type, string $message){
        $this->addFlash($type,$message);
    }
    protected function routeExists($name) : bool
    {
        // I assume that you have a link to the container in your twig extension class
        $router = $this->container->get('router');
        return (null === $router->getRouteCollection()->get($name)) ? false : true;
    }
    /* HTTP Parameters */
    protected function getParm(string $parm): mixed{
        // https://lindevs.com/methods-to-get-route-parameters-in-symfony
        $data =  $this->request->get($parm);
        return $data ??  WLConstants::NONE;
    }
    protected function postParm(string $parm, mixed $defaultValue = WLConstants::NONE): string{
        $parameters = json_decode($this->request->getContent(), true);
        return array_key_exists($parm,$parameters) ?  $parameters[$parm] :  $defaultValue;
    }
    protected function postJson(): mixed{
        return json_decode($this->request->getContent());
    }

    /**
     * Session Functions
     */
    protected function logout(){
        $this->session->clear();
    }
    protected function getSessionParm(string $parm, mixed $defaultValue = WLConstants::NONE) : string {
        $data =  $this->session->get($parm);
        return $data ?? $defaultValue;
    }
    protected function getSessionAllParms() : array{
        $data =  $this->session->all();
        return $data ?? [];
    }
    protected function setSessionParm(string $key, mixed $value=WLConstants::NONE){
        $this->session->set($key,$value);
    }
    protected function getQueryParm(string $parm): mixed{
        //https://lindevs.com/methods-to-get-route-parameters-in-symfony
        $data =  $this->request->query->get($parm);
        return $data ?? WLConstants::NONE;
    }
    protected function customResponse(Response $redirectResponse) : Response{
        //foreach($this->cookiesResponse as &$value)
        //    $this->log($value);
            //$redirectResponse->headers->setCookie($value);
        
        return $redirectResponse;
    }

     /**
     * Security Functions
     */

    public function checkPlacePermissions(array $placePermissionRequired, string $placeSlug = "", bool $checkPublicPermissions = false){
        /** 
         *  $this->setSessionParm(WLConstants::SESSION_AUTH_TOKEN,"1");
         *   $this->setSessionParm(WLConstants::SESSION_PHONE_TOKEN,$this->postParm("phone", ""));
         *   $this->setSessionParm(WLConstants::SESSION_REST_ADMIN_ROLE,"owner");
         *   $slugString="";
         *  foreach ($places as $place) {
         *       $slugString = $slugString ."|{$place->getSlug()}|,";
         *   }
         *   $this->setSessionParm(WLConstants::SESSION_REST_ADMIN_TOKEN,$slugString);
         *   $this->setSessionParm(WLConstants::SESSION_REST_ADMIN_ROLE,"admin");
         *   $this->setSessionParm(WLConstants::SESSION_REST_ADMIN_TOKEN, $placeSlug); 
        */
        
        //$this->logError(json_encode($this->getSessionAllParms()));

        if($checkPublicPermissions){ 
            if($this->getSessionParm(WLConstants::S_PLACE_PUBLIC,WLConstants::NONE)!=$placeSlug){
                throw new SecurityException("Access denied.", 
                        4033,
                        [   
                            "S_PLACE_PUBLIC"=>$this->getSessionParm(WLConstants::S_PLACE_PUBLIC,WLConstants::NONE)
                        ]);
            }
        }
        if($checkPublicPermissions){
            if($this->getSessionParm(WLConstants::S_PLACE_AUTHORIZED,WLConstants::NONE)!="1"){
                throw new SecurityException("Access denied.", 
                                            4030,
                                            [   
                                                "S_PLACE_AUTHORIZED"=>$this->getSessionParm(WLConstants::S_PLACE_AUTHORIZED,WLConstants::NONE)
                                            ]);
            }
            if($placeSlug && !str_contains($this->getSessionParm(WLConstants::S_PLACE_PLACESLUGS,WLConstants::NONE), $placeSlug)){
                throw new SecurityException("Access denied.", 
                                            4031,
                                            [   
                                                "S_PLACE_PLACESLUGS"=>$this->getSessionParm(WLConstants::S_PLACE_PLACESLUGS,WLConstants::NONE)
                                            ]);
            }
            if(!in_array($this->getSessionParm(WLConstants::S_PLACE_ROLE,WLConstants::NONE),$placePermissionRequired)){
                throw new SecurityException("Access denied.", 
                                            4032,
                                            [   
                                                "S_PLACE_ROLE"=>$this->getSessionParm(WLConstants::S_PLACE_ROLE,WLConstants::NONE)
                                            ]);
            }
        }

    }
    public function checkCustomerPermissions(array $placePermissionRequired, string $placeSlug = "", string $phone=""){
        /** 
         *  $this->setSessionParm(WLConstants::SESSION_AUTH_TOKEN,"1");
         *   $this->setSessionParm(WLConstants::SESSION_PHONE_TOKEN,$this->postParm("phone", ""));
         *   $this->setSessionParm(WLConstants::SESSION_REST_ADMIN_ROLE,"owner");
         *   $slugString="";
         *  foreach ($places as $place) {
         *       $slugString = $slugString ."|{$place->getSlug()}|,";
         *   }
         *   $this->setSessionParm(WLConstants::SESSION_REST_ADMIN_TOKEN,$slugString);
         *   $this->setSessionParm(WLConstants::SESSION_REST_ADMIN_ROLE,"admin");
         *   $this->setSessionParm(WLConstants::SESSION_REST_ADMIN_TOKEN, $placeSlug); 
        */

        if($this->getSessionParm(WLConstants::S_CUST_AUTHORIZED,WLConstants::NONE)!="1"){
            throw new SecurityException("Access denied.", 
                                        4030,
                                        [   
                                            "S_CUST_AUTHORIZED"=>$this->getSessionParm(WLConstants::S_CUST_AUTHORIZED,WLConstants::NONE)
                                        ]);
        }

        if($placeSlug && !str_contains($this->getSessionParm(WLConstants::S_CUST_PLACESLUG,WLConstants::NONE), $placeSlug)){
            throw new SecurityException("Access denied.", 
                                        4031,
                                        [   
                                            "S_CUST_PLACESLUG"=>$this->getSessionParm(WLConstants::S_CUST_PLACESLUG,WLConstants::NONE),
                                            "placeSlug" => $placeSlug
                                        ]);
        }

      
       
        if($phone && 
                ($this->getSessionParm(WLConstants::S_CUST_PHONE,WLConstants::NONE)!=$phone || 
                    $this->getSessionParm(WLConstants::S_CUST_PHONE,WLConstants::NONE)==WLConstants::NONE)){
            throw new SecurityException("Access denied.", 
                                        4032,
                                        [   
                                            "S_CUST_PHONE" => $this->getSessionParm(WLConstants::S_CUST_PHONE,WLConstants::NONE),
                                            "Phone" => $phone
                                        ]);
        }
       
    }
    /**
     * Create a Security Token
     *
     * @return AuthToken
     */
    protected function createSecurityToken():AuthToken{
        return AuthToken::Token($this->session->all());;
    }
   
    
    /* COOKIE FUNCTIONS */
    protected function addCookie(string $key,string $value, DateTimeInterface $expires, string $domain ="/", bool $secure = false ){
        $this->cookiesResponse[] =Cookie::create($key)
                ->withValue($value)
                ->withExpires($expires)
                ->withDomain("127.0.0.1")
                ->withSecure($secure);
    }

    protected function removeCookie(string $key){
        if(!$this->cookies)
            return;
        unset($this->cookies[$key]);
    }
    
 
}
