<?php

namespace RI5\Exception;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SecurityException extends BaseException 
{
    
    // Redefine the exception so message isn't optional
    public function __construct(string $message ="User unathorized, please login!",int $code = 9200, array $exceptionData=[], ?Throwable $previous = null) {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
        $this->__REDIRECTION_ROUTE__ = "CUST_Login";
        $this->__RESPONSE_CODE__ = Response::HTTP_UNAUTHORIZED;
        $this->exceptioName = __CLASS__;
        $this->exceptionData=$exceptionData;
    }

    
}