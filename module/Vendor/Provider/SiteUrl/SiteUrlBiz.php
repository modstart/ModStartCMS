<?php


namespace Module\Vendor\Provider\SiteUrl;


use Module\Vendor\Biz\BizTrait;

class SiteUrlBiz
{
    use BizTrait;

    /**
     * @return AbstractSiteUrlBiz[]
     */
    public static function all()
    {
        return self::listAll();
    }

    /**
     * @param $name
     * @return AbstractSiteUrlBiz
     */
    public static function get($name)
    {
        return self::getByName($name);
    }
}
