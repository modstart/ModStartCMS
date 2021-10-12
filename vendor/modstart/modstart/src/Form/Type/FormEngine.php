<?php


namespace ModStart\Form\Type;

use ModStart\Core\Type\BaseType;

class FormEngine implements BaseType
{
    /**
     * 基础列表引擎
     */
    const BASIC = 'basic';
    /**
     * 平铺的树状结构引擎
     */
    const TREE = 'tree';
    /**
     * 海量树状结构管理
     */
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