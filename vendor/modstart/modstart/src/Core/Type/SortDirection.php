<?php


namespace ModStart\Core\Type;


class SortDirection implements BaseType
{
    const UP = 'up';
    const DOWN = 'down';
    const TOP = 'top';
    const BOTTOM = 'bottom';

    public static function getList()
    {
        return [
            self::UP => L('Up'),
            self::DOWN => L('Down'),
            self::TOP => L('Top'),
            self::BOTTOM => L('Bottom'),
        ];
    }

}