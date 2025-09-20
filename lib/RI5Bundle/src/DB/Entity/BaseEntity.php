<?php

namespace RI5\DB\Entity;

use RI5\Services\Traits\UtilityTrait;

class BaseEntity 
{
    use UtilityTrait;
    public function jsonSerialize() : mixed
    {
        return get_object_vars($this);
    }

    public function __toString(): string
    {
        return "";//UtilityTrait::VarExport($this,true);
    }

   
}