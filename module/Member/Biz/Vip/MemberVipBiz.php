<?php


namespace Module\Member\Biz\Vip;


use Module\Vendor\Provider\BizTrait;

class MemberVipBiz
{
    use BizTrait;

    /**
     * @return AbstractMemberVipBiz[]
     */
    public static function all()
    {
        return self::listAll();
    }

    /**
     * @param $name
     * @return AbstractMemberVipBiz
     */
    public static function get($name)
    {
        return self::getByName($name);
    }

}
