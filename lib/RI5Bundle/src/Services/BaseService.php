<?php

namespace RI5\Services;

use RI5\DB\Repository\CustomerRepository;
use RI5\DB\Repository\OtpRepository;
use RI5\DB\Repository\ReservationArchiveRepository;
use RI5\DB\Repository\ReservationRepository;
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