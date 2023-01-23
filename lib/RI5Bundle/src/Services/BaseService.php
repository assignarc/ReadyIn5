<?php

namespace RI5\Services;

use RI5\Services\Traits\LoggerAwareTrait;

abstract class BaseService 
{
    protected $objectRepository;
    protected $container;
    use LoggerAwareTrait;
    
    public function __construct() 
    {
      
    }

}