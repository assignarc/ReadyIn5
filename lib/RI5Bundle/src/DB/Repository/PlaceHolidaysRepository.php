<?php

namespace RI5\DB\Repository;

use RI5\DB\Entity\PlaceHolidays;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlaceHolidays>
 *
 * @method PlaceHolidays|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaceHolidays|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaceHolidays[]    findAll()
 * @method PlaceHolidays[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaceHolidaysRepository  extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlaceHolidays::class);
    }

    public function save(PlaceHolidays $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PlaceHolidays $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PlaceHolidays[] Returns an array of PlaceHolidays objects
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

//    public function findOneBySomeField($value): ?PlaceHolidays
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
