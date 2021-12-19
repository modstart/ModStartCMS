<?php

class MNav
{
    public static function all($position = 'head')
    {
        $records = \Module\Nav\Util\NavUtil::listByPositionWithCache($position);
        foreach ($records as $i => $v) {
            $records[$i]['_attr'] = \Module\Nav\Type\NavOpenType::getBlankAttributeFromValue($v);
        }
        return $records;
    }
}