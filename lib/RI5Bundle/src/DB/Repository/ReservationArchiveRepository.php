<?php

namespace RI5\DB\Repository;

use DateTimeInterface;
use RI5\DB\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;
use RI5\DB\Entity\Data\ReservationStatus;
use RI5\DB\Entity\ReservationArchive;

/**
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationArchiveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function save(ReservationArchive $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ReservationArchive $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

 
    public function getAllByDate(DateTimeInterface $minReservationDt, 
                                    DateTimeInterface $maxReservationDt, 
                                    DateTimeInterface $minStatusDt = null, 
                                    DateTimeInterface $maxStatusDt = null, 
                                    array $inReservationStatuses = null): ?array{

        $qb = $this->getEntityManager()->createQueryBuilder()
                    ->select('r')
                    ->from('RI5\DB\Entity\ReservationArchive', 'r')
                    ->where('r.reservationDt BETWEEN :minReservationDt AND :maxReservationDt')
                    ->setParameter('minReservationDt', $minReservationDt, Types::DATETIME_MUTABLE)
                    ->setParameter('maxReservationDt', $maxReservationDt,Types::DATETIME_MUTABLE);

        if($minStatusDt && $maxStatusDt){
                $qb = $qb->andWhere('(r.statusDt BETWEEN :minStatusDt AND :maxStatusDt) OR r.statusDt IS NULL')
                        ->setParameter('minStatusDt', $minStatusDt, Types::DATETIME_MUTABLE)
                        ->setParameter('maxStatusDt', $maxStatusDt,Types::DATETIME_MUTABLE);
        }
       

        if($inReservationStatuses && count($inReservationStatuses) > 0){
            //$qb = $qb->add('where', $qb->expr()->in('r.status', array('?1')));
            $reservationStatuses =[];
            foreach($inReservationStatuses as $in){
                $reservationStatuses[] = "" . $in->value . "";
            }
            $qb->andWhere('r.status IN (:reservationStatuses)');
            $qb->setParameter('reservationStatuses', $reservationStatuses);
        }
        $qb->andWhere('r.status NOT IN (:completStatus)');
        $qb->setParameter('completStatus', ReservationStatus::STATUS_COMPLETE->value);

       // $reservations[] = $qb->getQuery()->getResult();
        return $qb->getQuery()->getResult();
    }

//    public function findOneBySomeField($value): ?Reservation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
