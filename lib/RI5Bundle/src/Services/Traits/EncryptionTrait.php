<?php
namespace RI5\Services\Traits;


trait EncryptionTrait{

   
 
   
    /**
     * Summary of Encode  //https://gist.github.com/adalenv/4d89c0ae3f6a1b08261ae64ef213942d
     * @param mixed $string
     * @param mixed $key
     * @return string
     */
    public static function Encode($string,$key = "KEY4d89c0ae") : string {
        $key = sha1($key);
        $strLen = strlen($string);
        $keyLen = strlen($key);
        $j = 0;
        $hash = '';
        for ($i = 0; $i < $strLen; $i++) {
            $ordStr = ord(substr($string,$i,1));
            if ($j == $keyLen) { $j = 0; }
            $ordKey = ord(substr($key,$j,1));
            $j++;
            $hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));
        }
        return base64_encode($hash);
    }
   
    /**
     * Summary of Decode  //https://gist.github.com/adalenv/4d89c0ae3f6a1b08261ae64ef213942d
     * @param mixed $inString
     * @param mixed $key
     * @return string
     */
    public static function Decode($inString,$key = "KEY4d89c0ae") :string {
        $string = base64_decode($inString);
        $key = sha1($key);
        $strLen = strlen($string);
        $keyLen = strlen($key);
        $j = 0;
        $hash = '';
        for ($i = 0; $i < $strLen; $i+=2) {
            $ordStr = hexdec(base_convert(strrev(substr($string,$i,2)),36,16));
            if ($j == $keyLen) { $j = 0; }
            $ordKey = ord(substr($key,$j,1));
            $j++;
            $hash .= chr($ordStr - $ordKey);
        }
        return $hash;
    }
}