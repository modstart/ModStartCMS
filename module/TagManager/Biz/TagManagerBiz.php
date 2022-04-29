<?php


namespace Module\TagManager\Biz;

use Module\Vendor\Biz\BizTrait;

class TagManagerBiz
{
    use BizTrait;

    /**
     * @return AbstractTagManagerBiz[]
     */
    public static function all()
    {
        return self::listAll();
    }

    /**
     * @param $name
     * @return AbstractTagManagerBiz
     */
    public static function get($name)
    {
        return self::getByName($name);
    }
}
