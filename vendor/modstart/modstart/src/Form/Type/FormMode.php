<?php


namespace ModStart\Form\Type;


use ModStart\Core\Type\BaseType;

class FormMode implements BaseType
{
    const FORM = 'form';
    const ADD = 'add';
    const EDIT = 'edit';
    const DELETE = 'delete';
    const SORT = 'sort';

    public static function getList()
    {
        return [
            self::FORM => self::FORM,
            self::ADD => self::ADD,
            self::EDIT => self::EDIT,
            self::DELETE => self::DELETE,
            self::SORT => self::SORT,
        ];
    }

}