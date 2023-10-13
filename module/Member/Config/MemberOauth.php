<?php


namespace Module\Member\Config;


use ModStart\Core\Exception\BizException;
use Module\Member\Oauth\AbstractOauth;

/**
 * 用户第三方授权登录配置
 */
class MemberOauth
{
    private static $list = [];

    public static function register($item)
    {
        self::$list[] = $item;
    }

    public static function hasEnableItems()
    {
        $items = self::get();
        return !empty($items);
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
     * @return AbstractOauth|AbstractOauth[]|null
     * @throws BizException
     */
    public static function get($name = null)
    {
        static $list = null;
        if (null === $list) {
            $list = [];
            foreach (self::$list as $item) {
                if (is_string($item)) {
                    $item = [app($item)];
                } else if ($item instanceof \Closure) {
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
        return null;
    }

    public static function getByOauthKey($name)
    {
        foreach (self::get() as $oauth) {
            if ($oauth->oauthKey() == $name) {
                return $oauth;
            }
        }
        return null;
    }

    public static function getOrFail($name)
    {
        $oauth = self::get($name);
        BizException::throwsIfEmpty('授权登录信息(' . $name . ')未找到', $oauth);
        return $oauth;
    }

    private static function sort()
    {
        static $sort = 1000;
        return $sort++;
    }

}
