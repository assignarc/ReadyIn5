<?php

namespace RI5\DB\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use RI5\DB\Entity\PlaceCapacity;

/**
 * @extends ServiceEntityRepository<PlaceCapacity>
 *
 * @method PlaceCapacity|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaceCapacity|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaceCapacity[]    findAll()
 * @method PlaceCapacity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaceCapacityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlaceCapacity::class);
    }

    public function save(PlaceCapacity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PlaceCapacity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PlaceCapacity[] Returns an array of PlaceCapacity objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PlaceCapacity
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
