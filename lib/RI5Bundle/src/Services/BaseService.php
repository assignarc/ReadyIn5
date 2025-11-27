<?php

namespace RI5\Services;

use RI5\Services\Traits\CacheAwareTrait;


class BaseService 
{
    protected $objectRepository;
    protected $container;

    use CacheAwareTrait;
    public function __construct() 
    {
      
    }
    

}