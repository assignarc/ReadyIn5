<?php

namespace RI5\DB\Entity\Data;

use JsonSerializable;

class WLHeaders extends BaseObject implements JsonSerializable
{
   
    protected array $headers;

    public function __construct(mixed $headers =[]){
       
        $this->headers =$headers;
    }
    public function set(array $headers){
        $this->headers= $headers;
    }
    public function addHeader(string $key, mixed $detail){
        $this->headers[$key] = $detail;
    }
    public function get():mixed{
        return $this->headers;
    }
   
    public function jsonSerialize() :mixed
    {
        return get_object_vars($this);
    }

}