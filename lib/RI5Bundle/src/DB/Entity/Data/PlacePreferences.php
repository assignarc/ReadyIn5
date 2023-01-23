<?php

namespace RI5\DB\Entity\Data;

use RI5\DB\Entity\PlacePrefs;

class PlacePreferences{
    //          VALUE               => [datatype, User Editable, Unit, DisplayName]
   public $Prefs= [
                "PLAN_NAME"         => ["string",  true,   ""                   ,"Plan Name"                    ],
                "TOTAL_CAPACITY"    => ["int",     true,   "adults/children"    ,"Total Capacity"               ],
                "NUMBER_OF_TABLES"  => ["int",     true,   "count"              ,"Number of Tables"             ],
                "ESTIMATED_WAIT"    => ["int",     true,   "minutes"            ,"Estimated Waittime"           ],
                "ACTIVE"            => ["bool",    false,  ""                   ,"Subscription Active"          ],
    ];
   

}