<?php

namespace RI5\DB\Entity\Data;

 class WL{
    public  array $STATUS_TRANSLATIONS = array(
                                    ReservationStatus::STATUS_CALL->value => "Ready",
                                    ReservationStatus::STATUS_CANCEL->value => "Canceled",
                                    ReservationStatus::STATUS_EMPTY->value => "Empty",
                                    ReservationStatus::STATUS_EXPIRED->value => "Expired",
                                    ReservationStatus::STATUS_NOSHOW->value => "marked No Show",
                                    ReservationStatus::STATUS_SEAT->value => "Seated",
                                    ReservationStatus::STATUS_SERVE->value => "Served",
                                    ReservationStatus::STATUS_WAIT->value => "Waiting",
                                );
    public static array $SESSION_VALUES = array();
}
