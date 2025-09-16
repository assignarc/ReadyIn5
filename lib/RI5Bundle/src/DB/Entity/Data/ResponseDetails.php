<?php

namespace RI5\DB\Entity\Data;

use JsonSerializable;

class ResponseDetails extends BaseObject implements JsonSerializable
{
    protected string $text;
    protected int $code;
    protected array $details;

    public function __construct(string $message = "Success", int $code = 0, mixed $details =[]){
        $this->text=$message;
        $this->code=$code;
        $this->details =$details;
    }

    public function set(string $message = "Success", int $code = 0, mixed $details =[]){
        $this->text=$message;
        $this->code=$code;
        $this->details =$details;
    }

    public function addDetail(string $key, mixed $detail){
        $this->details[$key] = $detail;
    }
    public function setCode(int $code){
        $this->code = $code;
    }
    public function setMessage(string $message){
        $this->text = $message;
    }

    public function jsonSerialize() :mixed
    {
        return get_object_vars($this);
    }

}