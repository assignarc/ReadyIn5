<?php

namespace RI5\DB\Repository;
use PDO;
use RI5\DB\Entity\PlaceHolidays;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use RI5\Exception\DatabaseException;


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

    public function persistHoliday(PlaceHolidays $holiday): void
    {
        //         INSERT INTO `waitlist`.`place_holidays`
        // (`holidayid`,
        // `placeid`,
        // `holiday_date`,
        // `holiday_name`,
        // `special_note`)
        // VALUES
        // (<{holidayid: }>,
        // <{placeid: }>,
        // <{holiday_date: }>,
        // <{holiday_name: }>,
        // <{special_note: }>);

        $sql = "INSERT INTO `waitlist`.`place_holidays` 
                            (`placeid`, `holiday_date`, `holiday_name`,`special_note`) 
                    VALUES
                        (:placeid, :holiday_date,:holiday_name,:special_note)";
        try{
            $conn = $this->getEntityManager()->getConnection();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':placeid', $holiday->getPlace()->getPlaceid());
            $stmt->bindValue(':holiday_date', $holiday->getHolidayDate()->format('Y-m-d'), PDO::PARAM_STR);
            $stmt->bindValue(':holiday_name', $holiday->getHolidayName());
            $stmt->bindValue(':special_note', $holiday->getSpecialNote());
            $resultSet = $stmt->executeQuery();
        }
        catch (\Exception $e){
            throw new DatabaseException(
                "Error saving holiday for placeid: " . $holiday->getPlace()->getPlaceid() . " Error: " . $e->getMessage(),
                0,
                [],
                $e
            );
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
