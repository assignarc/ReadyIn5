<?php

namespace RI5\Services\Traits;

use Exception;
use Psr\Log\LoggerTrait;

trait UtilityTrait {
    /**
     * Substitute the parameters in template from array
     *
     * @param string $_TEMPLATE_ // With {{0}} notations
     * @param array $_SUBS_ array of substitiutions.
     * @return string
     */

    public static function Substitute(string $_TEMPLATE_, array $_SUBS_): string{
        preg_match_all('/{{[0-9]}}/', $_TEMPLATE_, $matches);
        if(count($matches[0]) != count($_SUBS_))
            throw new Exception("Incorrect substitution of parameters : Expected -" . count($matches) . " Found-" . count($_SUBS_));
        
        for ($i = 0; $i < count($matches[0]); $i++) {
            $_TEMPLATE_ = str_replace($matches[0][$i], $_SUBS_[$i],$_TEMPLATE_);
        }
        return $_TEMPLATE_;
    }
    /**
     * Obfucate Phone
     *
     * @param string $phone
     * @return string
     */
    public static function ObfuscatePhone(string $phone):string{
        return substr( $phone, 0, 3 ) // Get the first two digits
                .str_repeat( '*', ( strlen( $phone ) - 6 ) ) // Apply enough asterisks to cover the middle numbers
                .substr( $phone, - 3 ); // Get the last two digits
    }
    // public static function ObfuscateString(string $string):string{
    //     if(strlen($string) >= 3){
    //         return substr( $string, 0, 2 ) // Get the first two digits
    //             .str_repeat( '~', ( strlen( $string ) - 3 ) ) // Apply enough asterisks to cover the middle numbers
    //             .substr( $string, - 1 ); // Get the last two digits
    //     }
    //     else 
    //         return "***";
    // }

    /**
     * Obfuscate
     *
     * @param string $string
     * @param integer $leader
     * @param integer $trailer
     * @param string $replacement
     * @return string
     */
    public static function Obfuscate(string $string,int $leader=2,int $trailer=2, string $replacement="~"):string{
        if(strlen($string) >= ($leader+$trailer)){
            return substr( $string, 0, $leader ) // Get the first Leader Letters
                .str_repeat($replacement, (strlen( $string ) - ($leader+$trailer))) // Apply enough asterisks/replacements to cover the middle numbers
                .substr( $string, - $trailer ); // Get the last Trailer Letters
        }
        else 
            return str_repeat($replacement, strlen( $string ));
    }
    /**
     * Get String of max length specified.
     *
     * @param string $string
     * @param int $length
     * @return string
     */
    public static function MaxLength(string $string, int $length) : string {
        return (strlen($string) > $length)?substr($string, 0, $length):$string;
    }

    /**
     * Remove duplicates from Array
     *
     * @param [type] $places
     * @param boolean $keep_key_assoc
     * @return array
     */
    public static function UniquePlaces($places, $keep_key_assoc = true): array{
        //https://stackoverflow.com/questions/10505760/remove-duplicates-from-an-array-based-on-object-property
       return  array_intersect_key($places, array_unique(array_column($places, 'slug')));
    }

    /**
     * Custom VarExport 
     *
     * @param [type] $var
     * @param string $indent
     * @return void
     */
    public static function VarExport($var, $indent="") {
        //https://stackoverflow.com/questions/24316347/how-to-format-var-export-to-php5-4-array-syntax
        switch (gettype($var)) {
            case "string":
                return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';
            case "array":
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = "$indent    "
                         . ($indexed ? "" : UtilityTrait::VarExport($key) . " => ")
                         . UtilityTrait::VarExport($value, "$indent    ");
                }
                return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
            case "boolean":
                return $var ? "TRUE" : "FALSE";
            default:
                return var_export($var, TRUE);
        }
    }
}