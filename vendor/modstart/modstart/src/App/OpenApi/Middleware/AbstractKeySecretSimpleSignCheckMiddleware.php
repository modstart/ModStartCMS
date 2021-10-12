<?php

namespace ModStart\App\OpenApi\Middleware;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\SignUtil;

abstract class AbstractKeySecretSimpleSignCheckMiddleware
{

    abstract protected function getSecretByKey($key);

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $sign = Input::get('sign');
        $timestamp = Input::get('timestamp');
        $key = Input::get('key');

        if (empty($key)) {
            return Response::json(-1, 'key empty');
        }

        $secret = $this->getSecretByKey($key);
        if (empty($secret)) {
            return Response::json(-1, 'invalid key');
        }

        if (empty($timestamp)) {
            return Response::json(-1, 'timestamp empty');
        }

        if (($timestamp < time() - 1800 || $timestamp > time() + 1800) && empty($apiApp['isDemo'])) {
            return Response::json(-1, 'timestamp not valid (' . time() . ')');
        }

        if (empty($sign)) {
            return Response::json(-1, 'sign empty');
        }

        $params = [
            'timestamp' => $timestamp,
            'key' => $key,
        ];
        $signCalc = SignUtil::common($params, $secret);
        if ($sign != $signCalc) {
            Log::info('sign not match : ' . $signCalc);
            $ret = $this->signNotMatch();
            if ($ret['code']) {
                return $ret['data'];
            }
        }

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

        Session::flash('_openApiKey', $key);
        Session::flash('_openApiSecret', $secret);

        return $next($request);
    }

    protected function signNotMatch()
    {
        return Response::generate(-1, 'sign error', Response::json(-1, 'sign error'));
    }
}
