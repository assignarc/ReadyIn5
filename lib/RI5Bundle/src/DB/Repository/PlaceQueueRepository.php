<?php

namespace RI5\DB\Repository;

use RI5\DB\Entity\PlaceQueue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use RI5\Exception\DatabaseException;

/**
 * @extends ServiceEntityRepository<PlaceQueue>
 *
 * @method PlaceQueue|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlaceQueue|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlaceQueue[]    findAll()
 * @method PlaceQueue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaceQueueRepository  extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlaceQueue::class);
    }

    public function save(PlaceQueue $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PlaceQueue $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
 public function insertQueue(PlaceQueue $queue): void
    {
        // INSERT INTO `waitlist`.`place_queue`
        //     (`queueid`,
        //     `queuename`,
        //     `placeid`,
        //     `capacity_adults`,
        //     `capacity_children`,
        //     `capcity_total`)
        //     VALUES
        // (<{queueid: }>,
        // <{queuename: }>,
        // <{placeid: }>,
        // <{capacity_adults: }>,
        // <{capacity_children: }>,
        // <{capcity_total: }>);


        $sql = "INSERT INTO `waitlist`.`place_queue`
                    (`queuename`,
                    `placeid`,
                    `capacity_adults`,
                    `capacity_children`,
                    `capcity_total`)
                    VALUES
                    (:queuename,:placeid,:capacity_adults,:capacity_children,:capcity_total);";
        try{
            $conn = $this->getEntityManager()->getConnection();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':queuename', $queue->getQueuename());
            $stmt->bindValue(':placeid', $queue->getPlaceid());
            $stmt->bindValue(':capacity_adults', $queue->getCapacityAdults());
            $stmt->bindValue(':capacity_children', $queue->getCapacityChildren());
            $stmt->bindValue(':capcity_total', $queue->getCapcityTotal());
            $resultSet = $stmt->executeQuery();
        }
        catch (\Exception $e){
            throw new DatabaseException(
                "Error saving Queue for placeid: " . $queue->getPlaceid() . " Error: " . $e->getMessage(),
                0,
                [],
                $e
            );
        }
    }
     public function updateQueue(PlaceQueue $queue, $queueid): void
    {
        // UPDATE `waitlist`.`place_queue`
        //     SET
        //     `queueid` = <{queueid: }>,
        //     `queuename` = <{queuename: }>,
        //     `placeid` = <{placeid: }>,
        //     `capacity_adults` = <{capacity_adults: }>,
        //     `capacity_children` = <{capacity_children: }>,
        //     `capcity_total` = <{capcity_total: }>
        //     WHERE `queueid` = <{expr}>;

        

        $sql = "UPDATE `waitlist`.`place_queue` 
                    SET
                    `queuename` = :queuename,
                    `placeid` = :placeid,
                    `capacity_adults` = :capacity_adults,
                    `capacity_children` = :capacity_children,
                    `capcity_total` = :capcity_total
                    WHERE `queueid` = :queueid";
        try{
            $conn = $this->getEntityManager()->getConnection();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':queuename', $queue->getQueuename());
            $stmt->bindValue(':placeid', $queue->getPlaceid());
            $stmt->bindValue(':capacity_adults', $queue->getCapacityAdults());
            $stmt->bindValue(':capacity_children', $queue->getCapacityChildren());
            $stmt->bindValue(':capcity_total', $queue->getCapcityTotal());
            $stmt->bindValue(':queueid', $queueid);
            $resultSet = $stmt->executeQuery();
        }
        catch (\Exception $e){
            throw new DatabaseException(
                "Error saving Queue for placeid: " . $queue->getPlaceid() . " Error: " . $e->getMessage(),
                0,
                [],
                $e
            );
        }
    }
//    /**
//     * @return PlaceQueue[] Returns an array of PlaceQueue objects
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

//    public function findOneBySomeField($value): ?PlaceQueue
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
