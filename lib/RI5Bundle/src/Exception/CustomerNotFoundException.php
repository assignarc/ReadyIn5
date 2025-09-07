<?php

namespace RI5\Exception;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CustomerNotFoundException extends BaseException  {
    
    // Redefine the exception so message isn't optional
    public function __construct(string $message ="Customer not found!", int $code = 9900, array $exceptionData=[], ?Throwable $previous) {
        // make sure everything is assigned properly
        parent::__construct(message: $message, code: $code, previous: $previous);
        $this->__REDIRECTION_ROUTE__ = "CUST_Login";
        $this->__RESPONSE_CODE__ = Response::HTTP_NOT_FOUND;
        $this->exceptioName = __CLASS__;
        $this->exceptionData = $exceptionData;
    }

    // custom string representation of object
    public function __toString(): string {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}