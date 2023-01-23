<?php
namespace RI5\Services\Traits;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait EntityAwareTrait{
   
    private EntityManagerInterface $entityManager;

    #[Required]
    public function setEntityManager(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }
}
