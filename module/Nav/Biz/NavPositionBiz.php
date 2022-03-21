<?php


namespace Module\Nav\Biz;


use Module\Vendor\Biz\BizTrait;

class NavPositionBiz
{
    use BizTrait;

    /**
     * @return AbstractNavPositionBiz[]
     */
    public static function all()
    {
        return self::listAll();
    }

    /**
     * @param $name
     * @return AbstractNavPositionBiz
     */
    public static function get($name)
    {
        return self::getByName($name);
    }
}
