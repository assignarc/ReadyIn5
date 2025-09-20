<?php
namespace RI5\Services\Traits;

use Symfony\Component\Lock\LockFactory;
use Symfony\Contracts\Service\Attribute\Required;

trait LockerTrait{
    private LockFactory $lockFactory;
   
    #[Required]
    /**
     * Summary of setLocker
     * @param \Symfony\Component\Lock\LockFactory $lockFactory
     * @return void
     */
    public function setLocker(LockFactory $lockFactory){
        $this->lockFactory = $lockFactory;
    }

}
