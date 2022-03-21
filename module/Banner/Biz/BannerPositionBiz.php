<?php


namespace Module\Banner\Biz;


use Module\Vendor\Biz\BizTrait;

class BannerPositionBiz
{
    use BizTrait;

    /**
     * @return AbstractBannerPositionBiz[]
     */
    public static function all()
    {
        return self::listAll();
    }

    /**
     * @param $name
     * @return AbstractBannerPositionBiz
     */
    public static function get($name)
    {
        return self::getByName($name);
    }
}
