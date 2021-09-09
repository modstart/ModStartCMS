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
    ];

    
    public function handle($request, Closure $next)
    {
        $routeAction = Route::currentRouteAction();
        $pieces = explode('@', $routeAction);
        if (isset($pieces[0])) {
            $controller = $pieces[0];
        } else {
            $controller = null;
        }
        if (isset($pieces[1])) {
            $action = $pieces[1];
        } else {
            $action = null;
        }
        if (!Str::startsWith($controller, '\\')) {
            $controller = '\\' . $controller;
        }

        $adminUserId = intval(Session::get('_adminUserId', null));
        $adminUser = null;

        if (empty($adminUserId)) {
            $authAdminUserId = intval(Request::headerGet('auth-admin-user-id'));
            $authAdminTimestamp = intval(Request::headerGet('auth-admin-timestamp'));
            $authAdminSign = trim(Request::headerGet('auth-admin-sign'));
            if ($authAdminUserId > 0) {
                if ($authAdminTimestamp < time() - 1800 || $authAdminTimestamp > time() + 1800) {
                    return Response::send(-1, "auth-admin-timestamp error");
                }
                $authAdminUser = Admin::get($authAdminUserId);
                if (empty($authAdminUser)) {
                    return Response::send(-1, "admin user not exists");
                }
                if (empty($authAdminUser['password']) || empty($authAdminUser['passwordSalt'])) {
                    return Response::send(-1, "admin user forbidden");
                }
                $signCalc = md5("$authAdminUserId:$authAdminTimestamp:$authAdminUser[password]$authAdminUser[passwordSalt]");
                if ($signCalc != $authAdminSign) {
                    return Response::send(-1, 'admin user sign error');
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

        $controllerMethod = $controller . '@' . $action;
        if (!$this->isAuthIgnore($controller, $action)) {
            if ($adminUserId && !$adminUser) {
                Session::forget('_adminUserId');
                return Response::redirect(action('\ModStart\Admin\Controller\AuthController@login', ['redirect' => Request::currentPageUrl()]));
            }
            if (empty($adminUser)) {
                return Response::redirect(action('\ModStart\Admin\Controller\AuthController@login', ['redirect' => Request::currentPageUrl()]));
            }

                        $rules = array_build(AdminPermission::rules(), function ($k, $v) {
                return [$v, false];
            });

            if (AdminPermission::isFounder($adminUserId)) {
                foreach ($rules as $k => $v) {
                    $rules[$k] = true;
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
                    $rules[$rule] = true;
                }
                Session::put('_adminRules', $rules);
            }
            if (isset($rules[$controllerMethod]) && !$rules[$controllerMethod]) {
                return Response::send(-1, L('No Permission'));
            }
        }

        Session::put('_adminUserId', $adminUserId);
        Session::flash('_adminUser', $adminUser);

        View::share('_adminUser', $adminUser);
        View::share('_adminUserId', $adminUserId);
        View::share('_controllerMethod', $controllerMethod);

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
