<?php


namespace Module\Vendor\Provider\HomePage;


use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\AgentUtil;

class HomePageProvider
{
    /**
     * @var AbstractHomePageProvider[]
     */
    private static $instances = [
        DefaultHomePageProvider::class,
        DefaultMobileHomePageProvider::class,
    ];

    public static function register($provider)
    {
        self::$instances[] = $provider;
    }

    public static function registerQuick($title, $action, $type = [AbstractHomePageProvider::TYPE_PC, AbstractHomePageProvider::TYPE_MOBILE])
    {
        self::register(
            QuickHomePageProvider::make($title, $action, $type)
        );
    }

    /**
     * @return AbstractHomePageProvider[]
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

    public static function call($contextMethod, $defaultAction)
    {
        $controller = null;
        if (modstart_config('HomePage_Enable', false)) {
            if (AgentUtil::isMobile()) {
                $controller = modstart_config('HomePage_HomeMobile');
            }
            if (empty($controller)) {
                $controller = modstart_config('HomePage_Home');
            }
        }
        if (empty($controller)) {
            $controller = $defaultAction;
        }
        BizException::throwsIfEmpty('首页不存在', $controller);
        list($c, $a) = explode('@', $controller);
        list($contextC, $contextA) = explode('::', $contextMethod);
        if (!starts_with($contextC, '\\')) {
            $contextC = '\\' . $contextC;
        }
        if ($contextC == $c && $contextA == $a) {
            list($c, $a) = explode('@', $defaultAction);
        }
        $c = app($c);
        return app()->call([$c, $a]);
    }
}
