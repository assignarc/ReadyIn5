<?php

namespace RI5\Exception;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ReservationNotFoundException extends BaseException 
{
    
    // Redefine the exception so message isn't optional
    public function __construct($message ="Reservation not found!", $code = 9110, array $exceptionData=[], Throwable $previous = null) {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
        $this->__REDIRECTION_PAGE__ = "/";
        $this->__RESPONSE_CODE__ = Response::HTTP_NOT_FOUND;
        $this->exceptioName = __CLASS__;
        $this->exceptionData = $exceptionData;
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}