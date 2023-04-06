<?php


namespace Module\Partner\Biz;


use Module\Vendor\Provider\BizTrait;

class PartnerPositionBiz
{
    use BizTrait;

    public static function registerQuick($name, $title)
    {
        self::register(QuickPartnerPositionBiz::make($name, $title));
    }

    /**
     * @return AbstractPartnerPositionBiz[]
     */
    public static function all()
    {
        return self::listAll();
    }

    /**
     * @param $name
     * @return AbstractPartnerPositionBiz
     */
    public static function get($name)
    {
        return self::getByName($name);
    }
}
