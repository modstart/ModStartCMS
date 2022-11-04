<?php


namespace Module\Vendor\Provider\SiteUrl;


use ModStart\Core\Exception\BizException;

/**
 * 网站链接提供者
 * 当链接地址更新、创建、删除时，会自动调用该 Provider
 */
class SiteUrlProvider
{
    /**
     * @var AbstractSiteUrlProvider
     */
    private static $list = [];
    private static $init = false;

    public static function register($provider)
    {
        self::$list[] = $provider;
    }

    public static function get()
    {
        if (!self::$init) {
            self::$init = true;
            foreach (self::$list as $k => $v) {
                if (is_string($v)) {
                    self::$list[$k] = app($v);
                }
            }
        }
        return self::$list;
    }

    public static function update($url, $title = '', $param = [])
    {
        BizException::throwsIfEmpty('SiteUrlProvider.Error -> url empty', $url);
        foreach (self::get() as $instance) {
            /** @var AbstractSiteUrlProvider $instance */
            $instance->update($url, $title, $param);
        }
    }

    public static function delete($url)
    {
        BizException::throwsIfEmpty('SiteUrlProvider.Error -> url empty', $url);
        foreach (self::get() as $instance) {
            /** @var AbstractSiteUrlProvider $instance */
            $instance->delete($url);
        }
    }

}
