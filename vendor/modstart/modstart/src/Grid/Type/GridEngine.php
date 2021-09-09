<?php


namespace ModStart\Grid\Type;


use ModStart\Core\Type\BaseType;

class GridEngine implements BaseType
{
    const BASIC = 'basic';
    const TREE = 'tree';
    const TREE_MASS = 'treeMass';

    public static function getList()
    {
        return [
            self::BASIC => self::BASIC,
            self::TREE => self::TREE,
            self::TREE_MASS => self::TREE_MASS,
        ];
    }
}