<?php


namespace Module\Banner\Biz;


use Module\Vendor\Provider\BizTrait;

/**
 * Class BannerPositionBiz
 * @package Module\Banner\Biz
 *
 * @method static AbstractBannerPositionBiz[] listAll()
 */
class BannerPositionBiz
{
    use BizTrait;

    public static function registerQuick($name, $title, $remark = null)
    {
        self::register(QuickBannerPositionBiz::make($name, $title, $remark));
    }

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
