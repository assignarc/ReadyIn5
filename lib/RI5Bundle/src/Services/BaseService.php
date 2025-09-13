<?php

namespace RI5\Services;

use RI5\Services\Traits\CacheAwareTrait;


abstract class BaseService 
{
    protected $objectRepository;
    protected $container;

    use CacheAwareTrait;
    public function __construct() 
    {
      
    }
    

}