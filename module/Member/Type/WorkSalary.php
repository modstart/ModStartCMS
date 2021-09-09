<?php

namespace Module\Member\Type;

use ModStart\Core\Type\BaseType;

class WorkSalary implements BaseType
{

    public static function getList()
    {
        static $map = null;
        if (null === $map) {
            $map = [];
            foreach ([
                         "2000元以下",
                         "2000-3999元",
                         "4000-5999元",
                         "6000-9999元",
                         "10000-14999元",
                         "15000-19999元",
                         "20000-49999元",
                         "50000元以上",
                     ] as $item) {
                $map[$item] = $item;
            }
        }
        return $map;
    }

}