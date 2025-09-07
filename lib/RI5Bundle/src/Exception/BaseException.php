<?php

namespace RI5\Exception;

use Exception;
use JsonSerializable;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class BaseException extends Exception implements JsonSerializable
{
    protected string $__REDIRECTION_ROUTE__ = "CUST_Login";
    protected string $__REDIRECTION_PAGE__ ="/";
    protected int $__RESPONSE_CODE__ = Response::HTTP_INTERNAL_SERVER_ERROR;
    protected string $exceptioName = __CLASS__;
    protected array $exceptionData = [];
    // Redefine the exception so message isn't optional
   

    public function __construct(string $message, int $code = 1, Throwable $previous) {
        // make sure everything is assigned properly
        parent::__construct(message: $message, code: $code, previous: $previous);
    }

    // custom string representation of object
    public function __toString(): string {
        //return __CLASS__ . ":[{$this->code}]:{$this->message}\n";
        return __CLASS__ . ";CODE:{$this->code};MESSAGE:{$this->getMessage()};DATA:" . json_encode($this->exceptionData);
    }

    public function customFunction() {
        echo "A custom function for this type of exception\n";
    }

    public function getRedirectionRoute(): string {
        return $this->__REDIRECTION_ROUTE__;
    }
    public function getRedirectionPage(): string {
        return $this->__REDIRECTION_PAGE__;
    }

    public function getRediretion():string{
        return $this->getRedirectionRoute()=="" ? $this->getRedirectionPage() :  $this->getRedirectionRoute();
    }
    public function getResponseCode(): int
    {
        return $this->__RESPONSE_CODE__;
    }

    public static function CREATE(Exception $ex) : BaseException {
        if($ex instanceof BaseException)
            return $ex;
        else 
            return new BaseException($ex->getMessage(),1,$ex);
    }
   
    public function jsonSerialize() :mixed
    {
        return get_object_vars($this);
    }
}