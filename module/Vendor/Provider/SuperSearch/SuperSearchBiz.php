<?php


namespace Module\Vendor\Provider\SuperSearch;


use Module\Vendor\Provider\BizTrait;

class SuperSearchBiz
{
    use BizTrait;

    /**
     * @return AbstractSuperSearchBiz[]
     */
    public static function all()
    {
        return self::listAll();
    }

    /**
     * @param $name
     * @return AbstractSuperSearchBiz
     */
    public static function get($name)
    {
        return self::getByName($name);
    }
}
