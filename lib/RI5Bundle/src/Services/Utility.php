<?php

namespace RI5\Services;


class Utility extends BaseService 
{
   
    public function __construct() 
    {
        
    }
    public static function GOOGLE_ADDRESS_PARSE($addressComponents) : mixed {
        
        // $parts = array(
        //   'address'=>array('street_number','route'),
        //   'city'=>array('locality'),
        //   'state'=>array('administrative_area_level_1'),
        //   'zip'=>array('postal_code'),
        // );
        // if (!empty($addressComponents)) {
        //   $ac = $addressComponents;
        //   foreach($part as &$ac)
        //   // foreach($parts as $need->&$types) {
        //   //   foreach($ac as &$a) {
        //   //     if (in_array($a['types'][0],$types)) $address_out[$need] = $a['short_name'];
        //   //     elseif (empty($address_out[$need])) $address_out[$need] = '';
        //   //   }
        //  }
        // } else 
        //   return null;

        
        //return $address_out;
        return null;
      }
   
}