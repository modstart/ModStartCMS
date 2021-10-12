<?php


namespace ModStart\Core\Util;


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * 路由相关操作
 *
 * Class RouteUtil
 * @package ModStart\Core\Util
 * @since 1.5.0
 */
class RouteUtil
{
    /**
     * 解析当前访问路由的Controller和Method
     * @return array = [ controller, method ]
     */
    public static function parseControllerMethod()
    {
        $routeAction = Route::currentRouteAction();
        $pieces = explode('@', $routeAction);
        if (isset($pieces[0])) {
            $urlController = $pieces[0];
        } else {
            $urlController = null;
        }
        if (isset($pieces[1])) {
            $urlMethod = $pieces[1];
        } else {
            $urlMethod = null;
        }
        if (!Str::startsWith($urlController, '\\')) {
            $urlController = '\\' . $urlController;
        }

        return [
            $urlController,
            $urlMethod
        ];
    }
}
