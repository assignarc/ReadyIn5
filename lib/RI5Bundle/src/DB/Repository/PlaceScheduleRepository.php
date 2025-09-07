<?php

namespace RI5\DB\Repository;

use RI5\DB\Entity\PlaceSchedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use RI5\Services\Traits\LoggerAwareTrait;

/**
 * @extends ServiceEntityRepository<PlaceSchedule>
 *
 * @method PlaceSchedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaceSchedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaceSchedule[]    findAll()
 * @method PlaceSchedule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaceScheduleRepository  extends ServiceEntityRepository
{
    
    use LoggerAwareTrait;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlaceSchedule::class);
    }

    public function save(PlaceSchedule $entity, bool $flush = false): void
    {
        
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
        
    }

    public function remove(PlaceSchedule $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PlaceSchedule[] Returns an array of PlaceSchedule objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PlaceSchedule
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
