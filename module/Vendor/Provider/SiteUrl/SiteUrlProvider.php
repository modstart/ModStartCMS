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

    /**
     * 链接更新/新增触发
     * @param $url string 使用绝对路径，不要带 http 协议，如 /xxx/xxx
     * @param $title string 链接标题
     * @param $param array ['biz'=>'xxx']
     * @example
     * SiteUrlProvider::updateBiz('xxx', modstart_web_url('xxx/xxx'));
     * SiteUrlProvider::updateBiz('xxx', modstart_web_url('xxx/xxx'), 'xxx');
     * SiteUrlProvider::updateBiz('xxx', modstart_web_url('xxx/xxx'), 'xxx', ['biz'=>'xxx']);
     */
    public static function updateBiz($biz, $url, $title = '', $param = [])
    {
        $param['biz'] = $biz;
        BizException::throwsIfEmpty('SiteUrlProvider.Error -> url empty', $url);
        foreach (self::get() as $instance) {
            /** @var AbstractSiteUrlProvider $instance */
            $instance->update($url, $title, $param);
        }
    }

    /**
     * 链接更新/新增触发
     * @param $url string 使用绝对路径，不要带 http 协议，如 /xxx/xxx
     * @param $title string 链接标题
     * @param $param array ['biz'=>'xxx']
     * @example
     * SiteUrlProvider::update(modstart_web_url('xxx/xxx'));
     * SiteUrlProvider::update(modstart_web_url('xxx/xxx'), 'xxx');
     * SiteUrlProvider::update(modstart_web_url('xxx/xxx'), 'xxx', ['biz'=>'xxx']);
     * @deprecated delete at 2024-03-14
     */
    public static function update($url, $title = '', $param = [])
    {
        BizException::throwsIfEmpty('SiteUrlProvider.Error -> url empty', $url);
        foreach (self::get() as $instance) {
            /** @var AbstractSiteUrlProvider $instance */
            $instance->update($url, $title, $param);
        }
    }

    /**
     * 链接删除触发
     * @param $url string 使用绝对路径，不要带 http 协议，如 /xxx/xxx
     * @example
     * SiteUrlProvider::delete(modstart_web_url('xxx/xxx'));
     */
    public static function delete($url)
    {
        BizException::throwsIfEmpty('SiteUrlProvider.Error -> url empty', $url);
        foreach (self::get() as $instance) {
            /** @var AbstractSiteUrlProvider $instance */
            $instance->delete($url);
        }
    }

}
