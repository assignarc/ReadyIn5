<?php

namespace RI5\Exception;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ReservationStatusException extends BaseException 
{
    
    // Redefine the exception so message isn't optional
    public function __construct($message ="Invalid reservation status", $code = 9100, array $exceptionData=[],Throwable $previous = null) {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
        $this->__REDIRECTION_PAGE__ = "/";
        $this->__RESPONSE_CODE__ = Response::HTTP_NOT_FOUND;
        $this->exceptioName = __CLASS__;
        $this->exceptionData = $exceptionData;
    }

 
}