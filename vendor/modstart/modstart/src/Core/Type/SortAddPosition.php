<?php


namespace ModStart\Core\Type;


class SortAddPosition implements BaseType
{
    const HEAD = 'head';
    const TAIL = 'tail';

    public static function getList()
    {
        return [
            self::HEAD => 'Head',
            self::TAIL => 'Tail',
        ];
    }


}
