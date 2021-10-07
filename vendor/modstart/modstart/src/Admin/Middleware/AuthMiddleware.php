<?php

namespace ModStart\Admin\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use ModStart\Admin\Auth\Admin;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;

class AuthMiddleware
{
    protected $authIgnores = [
        '\ModStart\Admin\Controller\AuthController',
        '\ModStart\Admin\Controller\UtilController',
    ];

    
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

        
        
        if ($adminUserId) {
            if (empty($adminUser)) {
                $adminUser = Admin::get($adminUserId);
            }
        }

        $urlControllerMethod = $urlController . '@' . $urlMethod;
        if (!$this->isAuthIgnore($urlController, $urlMethod)) {
            if ($adminUserId && !$adminUser) {
                Session::forget('_adminUserId');
                return Response::redirect(action('\ModStart\Admin\Controller\AuthController@login', ['redirect' => Request::currentPageUrl()]));
            }
            if (empty($adminUser)) {
                return Response::redirect(action('\ModStart\Admin\Controller\AuthController@login', ['redirect' => Request::currentPageUrl()]));
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

            
            if (isset($rules[$urlControllerMethod])) {
                if (empty($rules[$urlControllerMethod]['auth'])) {
                    return Response::send(-1, L('No Permission'));
                }
            } else {
                $controllerRuleMap = array_filter(array_build($rules, function ($k, $rule) {
                    if (!AdminPermission::isUrlAction($rule['url'])) {
                        return null;
                    }
                    return [$rule['url'], $rule['rule']];
                }));
                
                if (isset($controllerRuleMap[$urlControllerMethod])) {
                    if (empty($rules[$controllerRuleMap[$urlControllerMethod]]['auth'])) {
                        return Response::send(-1, L('No Permission'));
                    }
                } else {
                    
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

        View::share('_adminUser', $adminUser);
        View::share('_adminUserId', $adminUserId);
        View::share('_controllerMethod', $urlControllerMethod);

        return $next($request);
    }

    private function isAuthIgnore($controller, $action)
    {
        foreach ($this->authIgnores as $item) {
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
