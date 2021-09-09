<?php

namespace Module\Member\Type;

use ModStart\Core\Type\BaseType;

class WorkPosition implements BaseType
{

    public static function getList()
    {
        static $map = null;
        if (null === $map) {
            $map = [];
            foreach ([
                         "普通职工",
                         "中层管理者",
                         "高层管理者",
                         "企业主",
                     ] as $item) {
                $map[$item] = $item;
            }
        }
        return $map;
    }

}