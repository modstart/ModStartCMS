<?php

namespace ModStart\Admin\Middleware;

use Closure;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use ModStart\Admin\Auth\Admin;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\App\Core\AccessGate;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;

class AuthMiddleware
{
    /**
     * @var AccessGate[]
     */
    private static $gates = [];

    public static function addGate($cls)
    {
        self::$gates[] = $cls;
    }

    protected $authIgnores = [
        '\ModStart\Admin\Controller\AuthController',
        '\ModStart\Admin\Controller\UtilController',
    ];

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
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

        $adminUserId = intval(Session::get('_adminUserId', null));
        $adminUser = null;

        /**
         * 对于使用API调用的处理逻辑
         */
        if (empty($adminUserId)) {
            $authAdminUserId = intval(Request::headerGet('auth-admin-user-id'));
            $authAdminTimestamp = intval(Request::headerGet('auth-admin-timestamp'));
            $authAdminRequestId = trim(Request::headerGet('auth-admin-request-id'));
            $authAdminSign = trim(Request::headerGet('auth-admin-sign'));
            if ($authAdminUserId > 0) {
                if ($authAdminTimestamp < time() - 1800 || $authAdminTimestamp > time() + 1800) {
                    return Response::json(-1, "auth-admin-timestamp error, server time " . time());
                }
                if (empty($authAdminRequestId)) {
                    return Response::json(-1, "request id empty");
                }
                $authAdminUser = Admin::get($authAdminUserId);
                if (empty($authAdminUser)) {
                    return Response::json(-1, "admin user not exists");
                }
                if (empty($authAdminUser['password']) || empty($authAdminUser['passwordSalt'])) {
                    return Response::json(-1, "admin user forbidden");
                }
                $md5String = "$authAdminTimestamp:$authAdminRequestId:$authAdminUserId:$authAdminUser[username]:$authAdminUser[password]:$authAdminUser[passwordSalt]";
                $signCalc = md5($md5String);
                if ($signCalc != $authAdminSign) {
                    return Response::json(-1, 'admin user sign error');
                }
                $adminUserId = $authAdminUser['id'];
                $adminUser = $authAdminUser;
            }
        }

        if ($adminUserId) {
            if (empty($adminUser)) {
                $adminUser = Admin::get($adminUserId);
            }
        }

        $urlControllerMethod = $urlController . '@' . $urlMethod;
        if (!$this->isAuthIgnore($urlController, $urlMethod)) {
            if ($adminUserId && !$adminUser) {
                Session::forget('_adminUserId');
                return Response::redirect(modstart_admin_url('login', ['redirect' => Request::currentPageUrl()]));
            }
            if (empty($adminUser)) {
                return Response::redirect(modstart_admin_url('login', ['redirect' => Request::currentPageUrl()]));
            }

            $rules = array_build(AdminPermission::rules(), function ($k, $v) {
                $v['auth'] = false;
                return [$k, $v];
            });
            if (AdminPermission::isFounder($adminUserId)) {
                foreach ($rules as $k => $v) {
                    $rules[$k]['auth'] = true;
                }
            } else {
                $adminHasRules = Session::get('_adminHasRules', []);
                if (true || (empty($adminHasRules) && $adminUser['id'] > 0) || $adminUser['ruleChanged']) {
                    if ($adminUser['ruleChanged']) {
                        Admin::ruleChanged($adminUser['id'], false);
                    }
                    $adminHasRules = [];
                    $ret = Admin::listRolesByUserId($adminUser['id']);
                    foreach ($ret['data'] as $role) {
                        foreach ($role['rules'] as $rule) {
                            $adminHasRules[$rule['rule']] = true;
                        }
                    }
                    Session::put('_adminHasRules', $adminHasRules);
                }
                foreach ($adminHasRules as $rule => $v) {
                    if (isset($rules[$rule])) {
                        $rules[$rule]['auth'] = true;
                    }
                }
            }
            Session::put('_adminRules', $rules);

            /**
             * 检查 action
             */
            if (isset($rules[$urlControllerMethod])) {
                if (empty($rules[$urlControllerMethod]['auth'])) {
                    if ($urlControllerMethod == '\App\Admin\Controller\IndexController@index') {
                        // 如果是首页，尝试跳转到第一个有权限的页面
                        foreach ($rules as $_ => $ruleInfo) {
                            if (!empty($ruleInfo['auth'])) {
                                // 如果是 URL，直接跳转，如果是 Controller@method，尝试调用
                                $url = $ruleInfo['url'];
                                if (AdminPermission::isUrlAction($ruleInfo['url'])) {
                                    $url = action($ruleInfo['url']);
                                }
                                return Response::redirect($url);
                            }
                        }
                    }
                    return Response::send(-1, L('No Permission'));
                }
            } else {
                $controllerRuleMap = array_filter(array_build($rules, function ($k, $rule) {
                    if (!AdminPermission::isUrlAction($rule['url'])) {
                        return null;
                    }
                    return [$rule['url'], $rule['rule']];
                }));
                /**
                 * 尝试将 action 转换为自定义 rule 检查权限
                 */
                if (isset($controllerRuleMap[$urlControllerMethod])) {
                    if (empty($rules[$controllerRuleMap[$urlControllerMethod]]['auth'])) {
                        return Response::send(-1, L('No Permission'));
                    }
                } else {
                    /*
                     * 对于只有一个方法的 controller，尝试使用重新校验
                     * 1 默认使用 controller 的 index 方法
                     * 2 如果定义了 public static $PermitMethodMap 属性，使用该属性作为映射
                     * 2.1 currentMethod => checkMethod         使用 当前 Controller 的 checkMethod 检查权限
                     * 2.2 currentMethod => controller@method   使用 Controller@method 检查权限
                     * 2.3 currentMethod => @rule               使用 rule 检查权限
                     * 2.4 currentMethod => *                   忽略匹配不到时的权限检查
                     * 2.5 *             => *                   本 Controller 的所有方法匹配不到时忽略权限检查
                     */
                    $fallbackMethod = 'index';
                    if (property_exists($urlController, 'PermitMethodMap')) {
                        $map = $urlController::$PermitMethodMap;
                        if (!empty($map[$urlMethod])) {
                            $fallbackMethod = $map[$urlMethod];
                        } else {
                            if (!empty($map['*'])) {
                                $fallbackMethod = $map['*'];
                            }
                        }
                    }
                    if ('*' != $fallbackMethod) {
                        if (starts_with($fallbackMethod, '@')) {
                            $checkControllerMethod = substr($fallbackMethod, 1);
                        } else if (str_contains($fallbackMethod, '@')) {
                            $checkControllerMethod = $fallbackMethod;
                        } else {
                            $checkControllerMethod = $urlController . '@' . $fallbackMethod;
                        }
                        if (isset($rules[$checkControllerMethod])) {
                            if (empty($rules[$checkControllerMethod]['auth'])) {
                                return Response::send(-1, L('No Permission'));
                            }
                        } else {
                            if (isset($controllerRuleMap[$checkControllerMethod])) {
                                if (empty($rules[$controllerRuleMap[$checkControllerMethod]]['auth'])) {
                                    return Response::send(-1, L('No Permission'));
                                }
                            } else {
                                return Response::send(-1, L('No Permission'));
                            }
                        }
                    }
                }
            }
        }

        Session::put('_adminUserId', $adminUserId);
        Session::flash('_adminUser', $adminUser);

        $isTab = @boolval(Input::get('_is_tab', false));
        $tabSectionName = 'bodyContent';
        if ($isTab) {
            $tabSectionName = 'body';
        }

        View::share([
            '_adminUser' => $adminUser,
            '_adminUserId' => $adminUserId,
            '_controllerMethod' => $urlControllerMethod,
            '_isTab' => $isTab,
            '_isTabQuery' => $isTab ? '1' : '',
            '_tabSectionName' => $tabSectionName,
        ]);

        foreach (self::$gates as $item) {
            /** @var AccessGate $instance */
            $instance = app($item);
            $ret = $instance->check($request);
            if (Response::isError($ret)) {
                return $ret;
            }
        }

        return $next($request);
    }

    private function isAuthIgnore($controller, $action)
    {
        $buildInAuthIgnores = config('modstart.admin.authIgnores', []);
        $authIgnores = array_merge($this->authIgnores, $buildInAuthIgnores);
        foreach ($authIgnores as $item) {
            if (Str::contains($item, '@')) {
                if ($controller . '@' . $action == $item) {
                    return true;
                }
            } else {
                if ($controller == $item) {
                    return true;
                }
            }
        }
        return false;
    }

}
