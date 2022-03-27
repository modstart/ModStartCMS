<?php


namespace Module\Partner\Biz;


use Module\Vendor\Biz\BizTrait;

class PartnerPositionBiz
{
    use BizTrait;

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
