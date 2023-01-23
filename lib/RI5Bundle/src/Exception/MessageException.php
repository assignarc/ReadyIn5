<?php

namespace RI5\Exception;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class MessageException extends BaseException  {
    
    // Redefine the exception so message isn't optional
    public function __construct($message ="Error sending a Message", $code = 9400, array $exceptionData=[], Throwable $previous = null) {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
        $this->__REDIRECTION_ROUTE__ = "CUST_Login";
        $this->__RESPONSE_CODE__ = Response::HTTP_NOT_FOUND;
        $this->exceptioName = __CLASS__;
        $this->exceptionData = $exceptionData;
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}