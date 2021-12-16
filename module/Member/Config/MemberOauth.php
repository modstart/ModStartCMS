<?php


namespace Module\Member\Config;


use ModStart\Core\Exception\BizException;
use Module\Member\Oauth\AbstractOauth;

class MemberOauth
{
    private static $list = [];

    public static function register($item)
    {
        self::$list[] = $item;
    }

    public static function hasItems()
    {
        $items = self::get();
        $items = array_filter($items, function ($item) {
            /** @var $item AbstractOauth */
            return $item->hasRender();
        });
        return !empty($items);
    }

    /**
     * @param null $name
     * @return AbstractOauth|array|null
     * @throws BizException
     */
    public static function get($name = null)
    {
        static $list = null;
        if (null === $list) {
            $list = [];
            foreach (self::$list as $item) {
                if ($item instanceof \Closure) {
                    $item = call_user_func($item);
                }
                $list = array_merge($list, $item);
            }
        }
        if (null === $name) {
            return $list;
        }
        foreach ($list as $item) {
            if ($item->name() == $name) {
                return $item;
            }
        }
        BizException::throws('授权登录信息未找到');
    }

    private static function sort()
    {
        static $sort = 1000;
        return $sort++;
    }

}
