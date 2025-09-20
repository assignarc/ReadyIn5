<?php

namespace RI5\Services;

use RI5\DB\Entity\Place;
use RI5\DB\Entity\PlaceHolidays;
use RI5\DB\Entity\PlaceOwner;
use RI5\DB\Entity\PlaceSchedule;
use RI5\Exception\PlaceInvalidRequestException;
use RI5\DB\Repository\PlaceHolidaysRepository;
use RI5\DB\Repository\PlaceImageRepository;
use RI5\DB\Repository\PlaceOwnerRepository;
use RI5\DB\Repository\PlaceQueueRepository;
use RI5\DB\Repository\PlaceRepository;
use RI5\DB\Repository\PlaceScheduleRepository;
use RI5\DB\Repository\PlaceUserRepository;
use DateTime;
use Exception;
use RI5\DB\Entity\Data\WLConstants;
use RI5\DB\Entity\PlaceQueue;
use RI5\DB\Entity\PlaceUser;
use RI5\DB\Events\PlaceUpdated;
use RI5\Exception\BaseException;
use RI5\Exception\InvalidRequestException;
use RI5\Exception\PlaceOwnerNotFoundException;
use RI5\Services\Traits\CacheAwareTrait;
use RI5\Services\Traits\ConfigAwareTrait;
use RI5\Services\Traits\EntityAwareTrait;
use RI5\Services\Traits\EventDispatcherTrait;
use RI5\Services\Traits\UtilityTrait;
use Symfony\Contracts\Cache\ItemInterface;

class PlaceService extends BaseService
{
    use EntityAwareTrait;
    use CacheAwareTrait;
    use ConfigAwareTrait;
    use EventDispatcherTrait;
    use UtilityTrait;

    private PlaceHolidaysRepository $placeHolidaysRepository;
    private PlaceUserRepository $placeUserRepository;
    private PlaceQueueRepository $placeQueueRepository;
    private PlaceScheduleRepository $placeScheduleRepository;
    private PlaceImageRepository $placeImageRepository;
    private PlaceOwnerRepository $placeOwnerRepository;


    /**
     * Undocumented function
     *
     * @param PlaceRepository $placeRepository
     * @param PlaceHolidaysRepository $placeHolidaysRepository
     * @param PlaceUserRepository $placeUserRepository
     * @param PlaceQueueRepository $placeQueueRepository
     * @param PlaceScheduleRepository $placeScheduleRepository
     * @param PlaceImageRepository $placeImageRepository
     * @param PlaceOwnerRepository $placeOwnerRepository
     */
    public function __construct(
        PlaceRepository $placeRepository,
        PlaceHolidaysRepository $placeHolidaysRepository,
        PlaceUserRepository $placeUserRepository,
        PlaceQueueRepository $placeQueueRepository,
        PlaceScheduleRepository $placeScheduleRepository,
        PlaceImageRepository $placeImageRepository,
        PlaceOwnerRepository $placeOwnerRepository
    ) {
        $this->objectRepository = $placeRepository;
        $this->placeHolidaysRepository = $placeHolidaysRepository;
        $this->placeQueueRepository = $placeQueueRepository;
        $this->placeScheduleRepository = $placeScheduleRepository;
        $this->placeUserRepository = $placeUserRepository;
        $this->placeImageRepository = $placeImageRepository;
        $this->placeOwnerRepository = $placeOwnerRepository;
    }
    /**
     * Undocumented function
     *
     * @param string $phoneNumber
     * @return Place|null
     */
    public function findPlacesByOwner(PlaceOwner $owner): mixed
    {
        $owner = $this->placeOwnerRepository->findOneByOwnerid($owner->getOwnerid());
        if ($owner) {
            return $this->objectRepository->findByOwnerid($owner->getOwnerId());
        } else
            return null;
    }
    /**
     * Invalidates the cache for a specific place.
     *
     * @param Place|null $place      The Place entity instance, or null if not available.
     * @param string|null $placeid   The unique identifier of the place, or null.
     * @param string|null $placeSlug The slug of the place, or null.
     *
     * @return void
     */
    public function invalidatePlaceCache(?Place $place, ?string $placeid, ?string $placeSlug): void
    {
        if ($place) {
            $this->cache->invalidateTags(["PLACE.INFO." . $place->getSlug(), "PLACE.INFO." . $place->getPlaceId()]);
            return;
        }
        if ($placeid)
            $this->cache->invalidateTags(["PLACE.INFO." . $placeid]);

        if ($placeSlug)
            $this->cache->invalidateTags(["PLACE.INFO." . $placeSlug]);

    }
    /**
     *  Return Place from cache or DB
     *
     * @param string $placeSlug
     * @param boolean $fromCache
     * @return Place|null
     */
    public function findPlace(string $placeSlug, bool $fromCache = false): ?Place
    {
        if (!$placeSlug || $placeSlug == WLConstants::NONE)
            return null;

        if (!$fromCache)
            $this->invalidatePlaceCache(place: null, placeid: null, placeSlug: $placeSlug);

        return $this->cache->get(
            "PLACE.INFO." . $placeSlug,
            function (ItemInterface $item) use ($placeSlug): mixed {
                $place = $this->objectRepository->findOneBySlug($placeSlug);
                $item->tag(['PLACE.INFO.' . $place->getSlug(), 'PLACE.INFO.' . $place->getPlaceId()]);
                $item->expiresAfter(intval($this->getConfigItem("RI5.GEN.cache.statusExpiry")));
                return $place;
            }
        );

    }
    /**
     * Return Place from cache or DB
     *
     * @param string $placeId
     * @param boolean $fromCache
     * @return Place
     */
    public function findPlaceById(string $placeId, bool $fromCache = false): Place
    {
        if (!$fromCache)
            $this->invalidatePlaceCache(place: null, placeid: $placeId, placeSlug: null);

        return $this->cache->get(
            "PLACE.INFO." . $placeId,
            function (ItemInterface $item) use ($placeId): mixed {
                $place = $this->objectRepository->findByPlaceId($placeId);
                $item->tag(['PLACE.INFO.' . $place->getSlug(), 'PLACE.INFO.' . $place->getPlaceId()]);
                $item->expiresAfter(intval($this->getConfigItem("RI5.GEN.cache.statusExpiry")));
                return $place;
            }
        );

    }
    /**
     * 
     * Generate a default queue name based on capacity and place id.
     */
    public function generateDefaultQueueName($capacityTotal, $capacityAdults, $capacityChildren, $placeId): string
    {
        return "Q-{$placeId}-{$capacityTotal}-{$capacityAdults}-{$capacityChildren}";
    }


    /**
     * Create or Update Place Holidays
     *
     * @param PlaceHolidays $holiday
     * @param string|null $holidayid
     * @return void
     */
    public function createUpdatePlaceHolidays(PlaceHolidays $holiday, ?string $holidayid = "")
    {
        if ($holidayid) {
            $holidayDB = $this->placeHolidaysRepository->findOneByHolidayid($holidayid);
            if ($holidayDB->getPlaceid() != $holiday->getPlaceid())
                throw new PlaceInvalidRequestException("Invalid Holiday Change request for the Place");
        } else
            $holidayDB = $this->placeHolidaysRepository->findOneBy(array(
                "placeid" => $holiday->getPlaceid(),
                "holidayDate" => $holiday->getHolidayDate(),
            ));
        if (!$holidayDB)
            $this->placeHolidaysRepository->save($holiday, true);
        else {
            $holidayDB->setPlaceId($holiday->getPlaceId())
                    ->setHolidayDate($holiday->getHolidayDate())
                    ->setHolidayName($holiday->getHolidayName())
                    ->setSpecialNote($holiday->getSpecialNote());

            $this->placeHolidaysRepository->persistHoliday($holidayDB);
             $this->dispatchEvent(new PlaceUpdated($$holidayDB->getPlace()), PlaceUpdated::NAME);
        }

    }
    public function createUpdatePlaceUser(PlaceUser $user, ?string $placeUserid = "")
    {
        if ($placeUserid) {
            $userDB = $this->placeUserRepository->findOneByPlaceuserid($placeUserid);
            if ($userDB->getPlaceid() != $user->getPlaceid())
                throw new PlaceInvalidRequestException("Invalid User Change request for the Place");
        } else
            $userDB = $this->placeUserRepository->findOneBy(array(
                        "placeid" => $user->getPlaceid(),
                        "username" => $user->getUsername(),
                    ));
        if (!$userDB)
            $this->placeUserRepository->save($user, true);
        else {
            $userDB->setPlaceId($user->getPlaceId())
                    ->setPhone($user->getPhone())
                    ->setAccessadmin($user->getAccessadmin())
                    ->setUsername($user->getUsername())
                    ->setAccessreservation($user->getAccessreservation());

            $this->placeUserRepository->save($userDB,true);
             $this->dispatchEvent(new PlaceUpdated($userDB->getPlace()), PlaceUpdated::NAME);
        }

    }
    /**
     * Summary of createUpdatePlaceSchedule
     * @param \RI5\DB\Entity\PlaceSchedule $schedule
     * @param mixed $scheduleid
     * @throws \RI5\Exception\PlaceInvalidRequestException
     * @return void
     */
    public function createUpdatePlaceSchedule(PlaceSchedule $schedule, ?string $scheduleid = "")
    {

        if ($scheduleid) {
            $scheduleDB = $this->placeScheduleRepository->findOneByScheduleid($scheduleid);
            if ($scheduleDB->getPlaceid() != $schedule->getPlaceid())
                throw new PlaceInvalidRequestException("Invalid Schedule Change request for the Place");
        } else {
            $scheduleDB = $this->placeScheduleRepository->findOneBy(array(
                "placeid" => $schedule->getPlaceid(),
                "day" => $schedule->getDay(),
                "shift" => $schedule->getShift()
            ));
        }

        if (!$scheduleDB) {
            $scheduleDB = new PlaceSchedule();
            $scheduleDB->setPlaceid($schedule->getPlaceid());

        }

        $scheduleDB->setDay($schedule->getDay())
                ->setOpenTime($schedule->getOpenTime())
                ->setCloseTime($schedule->getCloseTime())
                ->setShift($schedule->getShift());
        $this->placeScheduleRepository->save($scheduleDB, true);
         $this->dispatchEvent(new PlaceUpdated($scheduleDB->getPlace()), PlaceUpdated::NAME);
    }
    /**
     * Summary of createUpdateQueue
     * @param \RI5\DB\Entity\PlaceQueue $queue
     * @param mixed $queueid
     * @throws \RI5\Exception\PlaceInvalidRequestException
     * @return void
     */
    public function createUpdateQueue(PlaceQueue $queue, ?string $queueid = null)
    {
        $queueDB = null;

        if ($queueid) {
            $queueDB = $this->placeQueueRepository->findOneByQueueid($queueid);
            if ($queueDB->getPlaceid() != $queue->getPlaceid())
                throw new PlaceInvalidRequestException("Invalid Queue Change request for the Place");

            $queueDB->setQueuename($queue->getQueuename())
                    ->setCapacityAdults($queue->getCapacityAdults())
                    ->setCapacityChildren($queue->getCapacityChildren())
                    ->setCapcityTotal($queue->getCapcityTotal());
            $this->placeQueueRepository->updateQueue($queueDB, $queueid);

        } else {
            $this->placeQueueRepository->insertQueue($queue);
        }
    }
    /**
     * Get Place Owner
     *
     * @param string|null $phoneNumber
     * @param string|null $placeSlug
     * @param string|null $ownerId
     * @return PlaceOwner|null
     */
    public function getPlaceOwner(string $ownerPhoneNumber = "", string $placeSlug = "", string $ownerId = ""): ?PlaceOwner
    {

        if ($ownerPhoneNumber)
            return $this->placeOwnerRepository->findOneByPhone($ownerPhoneNumber);
        if ($placeSlug)
            return $this->findPlace($placeSlug, false)->getPlaceOwner();
        if ($ownerId)
            return $this->placeOwnerRepository->findOneByOwnerid($ownerId);

        throw new PlaceOwnerNotFoundException("Place or Owner not found!");

    }
    /**
     * Undocumented function
     *
     * @param PlaceOwner $owner
     * @return PlaceOwner|null
     */
    public function createUpdatePlaceOwner(PlaceOwner $owner): ?PlaceOwner
    {

        $ownerDB = $this->placeOwnerRepository->findOneByPhone($owner->getPhone());
        if (!$ownerDB) {
            $ownerDB = new PlaceOwner();
            // $ownerDB->setPlaces($owner->getPlaces());
        } else {
            if ($ownerDB->getPhone() != $owner->getPhone()) {
                throw new PlaceInvalidRequestException("Invalid Owner Change request for the Place");
            }
            // $places = array_merge($ownerDB->getPlaces(),$owner->getPlaces());
            // $ownerDB->setPlaces(UtilityTrait::UniquePlaces($places));
        }


        $ownerDB->setName($owner->getName())
                ->setAddressline1($owner->getAddressline1())
                ->setAddressline2($owner->getAddressline2())
                ->setAddressline3($owner->getAddressline3())
                ->setCity($owner->getCity())
                ->setState($owner->getState())
                ->setCountry($owner->getCountry())
                ->setPostalcode($owner->getPostalcode())
                ->setPhone($owner->getPhone())
                ->setEmail($owner->getEmail())
                ->setPhonevalidated($owner->isPhonevalidated())
                ->setEmailvalidated($owner->isEmailvalidated());

        $this->placeOwnerRepository->save($ownerDB, true);

        return $ownerDB;
    }
    /**
     * Undocumented function
     *
     * @param string $placeMetaEntityType
     * @param string $placeMetaEntityKey
     * @return void
     */
    public function removeMetaEntity(string $placeMetaEntityType, string $placeMetaEntityKey)
    {
        if (!$placeMetaEntityKey || !$placeMetaEntityType)
            throw new PlaceInvalidRequestException("Invalid Place Meta Entity request");
        switch ($placeMetaEntityType) {
            case "holidays":
                $holiday = $this->placeHolidaysRepository->findOneByHolidayid($placeMetaEntityKey);
                if (!$holiday)
                    throw new PlaceInvalidRequestException("Place Holiday not found, so can't be deleted");
                $this->placeHolidaysRepository->remove($holiday, true);
                break;
             case "queues":
                $queue = $this->placeQueueRepository->findOneByQueueid($placeMetaEntityKey);
                if (!$queue)
                    throw new PlaceInvalidRequestException("Place Holiday not found, so can't be deleted");
                $this->placeQueueRepository->remove($queue, true);
                break;
            default:
                break;
        }

    }


    /**
     * Undocumented function
     *
     * @param Place $place
     * @return void
     */
    public function createDefaultPlaceSchedule(Place $place)
    {
        foreach (array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday') as &$day) {
            $now = new DateTime();
            $now->setTime(0, 0, 0, 0);

            $schedule = new PlaceSchedule()
                    ->setPlace($place)
                    ->setDay($day)
                    ->setOpenTime($now->format("H:i:s"))
                    ->setCloseTime($now->format("H:i:s"))
                    ->setShift("default");

            $this->createUpdatePlaceSchedule($schedule);
        }
    }
      /* Get the default queue ID for a place.
      *
      * Returns the first queue's `queueid` associated with the place, relying on
      * `getPlaceMeta(..., "queues")` which may create a default queue if none exist.
      * Returns null if no queue is available after lookup.
      *
      * @param Place $place Place entity to evaluate
      * @return string|null Default queue ID or null when unavailable
      * @throws InvalidRequestException When the place is not found or unauthorized
      */
    public function getDefaultQueueId(Place $place)
    {
        // Example: If Place has a getPlaceQueues() method that returns an array of PlaceQueue objects
        $queues = $this->getPlaceMeta($place->getSlug(), "queues");
        if (is_array($queues) && count($queues) > 0) {
            // Assuming the first queue is the default, or implement your own logic
            return $queues[0]->getQueueid();
        }
        return null;
    }

    /**
     * Undocumented function
     *
     * @param Place $place
     * @return Place
     */
    public function createUpdatePlace(Place $place, ?PlaceOwner $placeOwner): Place
    {

        $placeDB = $this->findPlace($place->getSlug(), false);

        if (!$placeDB) {
            $placeDB = new Place();
            $placeDB->setSlug($place->getSlug());
        }

        $placeDB->setName($place->getName() ?? $placeDB->getName());
        $placeDB->setAddressline1($place->getAddressline1() ?? $placeDB->getAddressline1());
        $placeDB->setAddressline2($place->getAddressline2() ?? $placeDB->getAddressline2());
        $placeDB->setAddressline3($place->getAddressline3() ?? $placeDB->getAddressline3());
        $placeDB->setAddressline4($place->getAddressline4() ?? $placeDB->getAddressline4());
        $placeDB->setAddressline5($place->getAddressline5() ?? $placeDB->getAddressline5());
        $placeDB->setCity($place->getCity() ?? $placeDB->getCity());
        $placeDB->setState($place->getState() ?? $placeDB->getState());
        $placeDB->setCountry($place->getCountry() ?? $placeDB->getCountry());
        $placeDB->setPostalcode($place->getPostalcode() ?? $placeDB->getPostalcode());
        $placeDB->setPhone($place->getPhone() ?? $placeDB->getPhone());
        $placeDB->setAddressdata($place->getAddressdata() ?? $placeDB->getAddressdata());


        $this->entityManager->getConnection()->beginTransaction();
        try {
            if ($placeOwner) {
                // $placeOwner->setPlaces([$this->objectRepository->findOneBySlug($placeDB->getSlug())]);
                $placeOwner = $this->createUpdatePlaceOwner($placeOwner);
                $placeDB->setPlaceOwner($placeOwner);
            }
            $this->objectRepository->save($placeDB, true);
            $this->entityManager->getConnection()->commit();
        } catch (Exception $ex) {
            $this->entityManager->getConnection()->rollBack();
            throw $ex;
        }
        $this->dispatchEvent(new PlaceUpdated($placeDB), PlaceUpdated::NAME);

        return $this->findPlace($placeDB->getSlug(), false);
    }
    /**
     * Undocumented function
     *
     * @return void
     */
    public function findAll(): array
    {
        $data = $this->objectRepository->findAll();
        return $data;
    }
    /**
     * Summary of getPlaceMeta
     * @param string $placeSlug
     * @param string $reqType
     * @throws \RI5\Exception\InvalidRequestException
     * @return mixed
     */
    public function getPlaceMeta(string $placeSlug, string $reqType): mixed
    {

        try {
            // if($placeSlug!=$this->getSessionParm("REST-ADMIN-Hash", "EMPTY-PLACE"))
            //     throw new SecurityException("User unauthorized");

            //The place is checked upstream, so do we need this call to check again?
            $place = $this->findPlace($placeSlug, true);
            if (!$place) {
                throw new InvalidRequestException("Place not found or you are not authorized to manage it", 9310, [], null);
            }

            switch ($reqType) {
                case "holidays":
                    return $this->placeHolidaysRepository->findByPlaceid($place->getPlaceid());
                case "users":
                    return $this->placeUserRepository->findByPlaceid($place->getPlaceid());
                case "images":
                    return $this->placeImageRepository->findByPlaceid($place->getPlaceid());
                case "queues":
                    $queues = $this->placeQueueRepository->findByPlaceid($place->getPlaceid());
                    //Create default queue if not found
                    $count = $queues ? count($queues) : 0;
                    if ($count < 1) {
                        $this->createUpdateQueue(queue: new PlaceQueue()
                            ->setPlaceid($place->getPlaceid())
                            ->setCapacityAdults(40)
                            ->setCapacityChildren(10)
                            ->setCapcityTotal(50)
                            ->setQueuename("default"));
                        return $this->placeQueueRepository->findByPlaceid($place->getPlaceid());
                    } else
                        return $queues;
                case "schedules":
                    //Create default schedule if not found
                    $schedules = $this->placeScheduleRepository->findByPlaceid($place->getPlaceid());
                    $count = $schedules ? count($schedules) : 0;
                    if ($count != 7) {
                        $this->createDefaultPlaceSchedule($place);
                        return $this->placeScheduleRepository->findByPlaceid($place->getPlaceid());
                    } else
                        return $schedules;

                case "meta":
                    return $place;
                default:
                    throw new InvalidRequestException("Invalid Place request.", 9500, [], null);
            }

        } catch (Exception $ex) {
            $this->logException($ex);
            throw BaseException::CREATE($ex);
        }

    }
}