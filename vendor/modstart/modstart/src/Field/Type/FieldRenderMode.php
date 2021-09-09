<?php


namespace ModStart\Field\Type;


use ModStart\Core\Type\BaseType;


class FieldRenderMode implements BaseType
{
    const GRID = 'grid';
    const FORM = 'form';
    const DETAIL = 'detail';

    public static function getList()
    {
        return [
            self::GRID => self::GRID,
            self::FORM => self::FORM,
            self::DETAIL => self::DETAIL,
        ];
    }
}