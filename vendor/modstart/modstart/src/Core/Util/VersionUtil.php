<?php


namespace ModStart\Core\Util;

class VersionUtil
{
    
    public static function match($version, $targetVersionWithOperator)
    {
        if ('*' == $targetVersionWithOperator) {
            return true;
        }
        $support = ['>=', '<=', '==', '>', '<'];
        $operator = '==';
        foreach ($support as $item) {
            if (starts_with($targetVersionWithOperator, $item)) {
                $operator = $item;
                $targetVersionWithOperator = substr($targetVersionWithOperator, strlen($item));
                break;
            }
        }
        return version_compare($version, $targetVersionWithOperator, $operator);
    }

    
    public static function parse($nameVersion)
    {
        $pcs = explode(':', $nameVersion);
        if (count($pcs) == 1) {
            return [$pcs[0], '*'];
        }
        return [$pcs[0], $pcs[1]];
    }
}
