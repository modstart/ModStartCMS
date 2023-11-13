<?php


namespace Module\Vendor\Provider\SiteTemplate;


/**
 * 模板提供者，所有模板都需要在这里注册
 */
class SiteTemplateProvider
{
    /**
     * @var AbstractSiteTemplateProvider[]
     */
    private static $instances = [
        DefaultSiteTemplateProvider::class,
    ];

    public static function register($provider)
    {
        self::$instances[] = $provider;
    }

    public static function registerQuick($name, $title, $root = null)
    {
        self::register(QuickSiteTemplateProvider::make($name, $title, $root));
    }

    /**
     * @return AbstractSiteTemplateProvider[]
     */
    public static function all()
    {
        foreach (self::$instances as $k => $v) {
            if ($v instanceof \Closure) {
                self::$instances[$k] = call_user_func($v);
            } else if (is_string($v)) {
                self::$instances[$k] = app($v);
            }
        }
        return self::$instances;
    }

    /**
     * @param $name
     * @return AbstractSiteTemplateProvider|null
     * @since 1.9.0
     */
    public static function get($name)
    {
        foreach (self::all() as $provider) {
            if ($provider->name() == $name) {
                return $provider;
            }
        }
        return null;
    }

    public static function map()
    {
        return array_build(self::all(), function ($k, $v) {
            /** @var $v AbstractSiteTemplateProvider */
            return [$v->name(), $v->title()];
        });
    }
}
