<?php


namespace Module\Member\Biz\Card;


use Module\Vendor\Provider\BizTrait;

class MemberCardBiz
{
    use BizTrait;

    /**
     * @return AbstractMemberCardBiz[]
     */
    public static function all()
    {
        return self::listAll();
    }

    /**
     * @param $name
     * @return AbstractMemberCardBiz
     */
    public static function get($name)
    {
        return self::getByName($name);
    }

}
