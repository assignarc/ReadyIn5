<?php

namespace RI5\DB\Entity\Data;
use JsonSerializable;


class BaseObject implements JsonSerializable
{

    public function jsonSerialize(): mixed
    {
        $vars = get_object_vars($this);
        return $vars;
    }
  
}
