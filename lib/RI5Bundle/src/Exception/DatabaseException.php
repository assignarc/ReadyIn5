<?php

namespace RI5\Exception;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class DatabaseException extends BaseException  {
    
    // Redefine the exception so message isn't optional
    public function __construct(string $message ="Database Exception.",int $code = 9600, array $exceptionData=[], ?Throwable $previous) {
        // make sure everything is assigned properly        
        parent::__construct($message, $code, $previous);
        $this->__REDIRECTION_ROUTE__ = "";
        $this->__RESPONSE_CODE__ = Response::HTTP_INTERNAL_SERVER_ERROR;
        $this->exceptioName = __CLASS__;
        $this->exceptionData = $exceptionData;
    }

    // custom string representation of object
    public function __toString(): string {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}