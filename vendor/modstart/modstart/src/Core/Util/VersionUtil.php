<?php


namespace ModStart\Core\Util;


use ModStart\Core\Exception\BizException;

class VersionUtil
{
    
    public static function match($version, $targetVersionWithOperator)
    {
        if ('*' == $targetVersionWithOperator) {
            return true;
        }
        $support = ['>=', '<=', '==', '>', '<'];
        $operator = '>=';
        $found = false;
        foreach ($support as $item) {
            if (starts_with($targetVersionWithOperator, $item)) {
                $operator = $item;
                $targetVersionWithOperator = substr($targetVersionWithOperator, strlen($item));
                $found = true;
            }
        }
        BizException::throwsIf('Version Compare Failed', !$found);
        return version_compare($version, $targetVersionWithOperator, $operator);
    }
}
