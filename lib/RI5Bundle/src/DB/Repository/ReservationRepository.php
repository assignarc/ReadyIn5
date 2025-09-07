<?php

namespace RI5\DB\Repository;

use DateTimeInterface;
use RI5\DB\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;
use RI5\DB\Entity\Data\ReservationId;
use RI5\DB\Entity\Data\ReservationStatus;
use RI5\Exception\ReservationNotFoundException;
use RI5\Services\Traits\LoggerAwareTrait;

/**
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{

    use LoggerAwareTrait;


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function save(Reservation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Reservation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

 
    public function getAllByDate(DateTimeInterface $minReservationDt, 
                                DateTimeInterface $maxReservationDt, 
                                ?DateTimeInterface $minStatusDt = null, 
                                ?DateTimeInterface $maxStatusDt = null, 
                                ?array $inReservationStatuses = null,
                                ?array $notInReservationStatuses = null
                        ): ?array{

        $qb = $this->getEntityManager()->createQueryBuilder()
                    ->select('r')
                    ->from('RI5\DB\Entity\Reservation', 'r')
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

        if($notInReservationStatuses && count($notInReservationStatuses) > 0){
            //$qb = $qb->add('where', $qb->expr()->in('r.status', array('?1')));

             // $qb->andWhere('r.status NOT IN (:completStatus)');
             // $qb->setParameter('completStatus', ReservationStatus::STATUS_COMPLETE->value);

            $notReservationStatuses =[];

            foreach($notInReservationStatuses as $in){
                $notReservationStatuses[] = "" . $in->value . "";
            }
            $qb->andWhere('r.status NOT IN (:notreservationStatuses)');
            $qb->setParameter('notreservationStatuses', $notReservationStatuses);
        }
       

       // $reservations[] = $qb->getQuery()->getResult();
        return $qb->getQuery()->getResult();
    }
  
   /**
     * For creating new reservations, we must get the next available reservation id for the queue and place. 
     *
     * @param integer $day  // Pass 0 for Autogeneration
     * @param integer $restaurant
     * @param integer $queueId
     * @param integer $placeId
     * @param integer $tableCapacity
     */
    public function createNextReservationId(int $day, int $placeId, int $queueId,int $tableCapacity) : ReservationId
    {
        //Create dummy Res Id to get Min and Max
        $resId = new ReservationId(11,$placeId,$queueId, 0000, 0);
       
        $qb = $this->getEntityManager()->createQueryBuilder()
                    ->select('MAX(r.reservationid)')
                    ->from('RI5\DB\Entity\Reservation', 'r')
                    ->where('r.reservationid BETWEEN :minval AND :maxval')
                    ->setParameter('minval', $resId->getMinVal(), Types::INTEGER)
                    ->setParameter('maxval', $resId->getMaxVal(),Types::INTEGER);

        $maxId = $qb->getQuery()->getResult();
        //$this->logCritical("MaxID:" . $maxId[0][1]);
        if(empty($maxId) || !isset($maxId[0]) || !isset($maxId[0][1]))
            return ReservationId::GetNext($resId->id(), $tableCapacity);
        else
            return ReservationId::GetNext($maxId[0][1], $tableCapacity);       
    }
   /**
    * Find the first reservation waiting in the line for the queue and place specified. 
    *
    * @param integer $placeId
    * @param integer $queueId
    * @return ReservationId
    */
    public function findLastReservationByPlaceId(int $placeId, int $queueId):ReservationId {

        $baseReservationId = new ReservationId(99,$placeId,$queueId, 0000, 0);
      
        $qb = $this->getEntityManager()->createQueryBuilder()
                    ->select('MIN(r.reservationid)')
                    ->from('RI5\DB\Entity\Reservation', 'r')
                    ->where('r.reservationid BETWEEN :minval AND :maxval')
                    ->andWhere('r.status = :status')
                    ->setParameter('minval', $baseReservationId->getMinVal(), Types::INTEGER)
                    ->setParameter('maxval', $baseReservationId->getMaxVal(),Types::INTEGER)
                    ->setParameter('status', 'WAIT', Types::STRING);
        
        $latestWaitingReservationId = $qb->getQuery()->getResult();

        if(empty($latestWaitingReservationId) || !isset($latestWaitingReservationId[0]) || !isset($latestWaitingReservationId[0][1]))
            return $baseReservationId;
        else
            return ReservationId::from($latestWaitingReservationId[0][1]);

       // throw new ReservationNotFoundException("Unable to get last waiting reservation id. " + $baseReservationId);
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
