<?php

namespace RI5\DB\Entity\Data;

enum ReservationStatus : string
{
    case STATUS_WAIT = "WAIT";
    case STATUS_CALL = "CALL";
    case STATUS_SEAT = "SEAT";
    case STATUS_SERVE = "SERVE";
    case STATUS_NOSHOW = "NOSHOW";
    case STATUS_EXPIRED = "EXPIRED";
    case STATUS_CANCEL = "CANCEL";
    case STATUS_COMPLETE = "COMPLETE";

    case STATUS_EMPTY = "";

   
}