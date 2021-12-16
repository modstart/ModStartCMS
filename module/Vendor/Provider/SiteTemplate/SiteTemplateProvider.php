<?php


namespace Module\Vendor\Provider\SiteTemplate;


/**
 * Class SiteTemplateProvider
 * @package Module\Vendor\Provider\SiteTemplate
 * @since 1.5.0
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
