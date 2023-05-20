<?php namespace de\roccogossmann\php\core;

use Closure;

class Utils {
    
    public static function flattenArray(array $aMultimensionalArray, Closure $sKeyGenerator=null, string $_sPrefix="", array &$_output=[]) {

        if(!is_callable($sKeyGenerator)) $sKeyGenerator = fn($sKey, $sPrefix) => empty($sPrefix) ? $sKey : $sPrefix . "." . $sKey;

        foreach($aMultimensionalArray as $sKey => $mValue) {
            $sPrefix = $sKeyGenerator($sKey, $_sPrefix);
            if(is_array($mValue)) self::flattenArray($mValue, $sKeyGenerator, $sPrefix, $_output);
            else $_output[$sPrefix] = $mValue;
        }

        return $_output;
    }

}
