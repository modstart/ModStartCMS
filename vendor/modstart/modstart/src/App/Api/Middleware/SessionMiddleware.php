<?php


namespace ModStart\App\Api\Middleware;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

/**
 * API兼容Session处理
 * 使用 header 中的 api-token 作为验证信息，共用 Session 信息
 *
 * Class SessionMiddleware
 * @package ModStart\App\Api\Middleware
 */
class SessionMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        $setApiToken = false;
        $sessionRestart = false;
        /**
         * 主要使用 api-token 来标识当前会话
         * 如果出现 sessionId 和 api-token 不一致，使用 api-token 替代 sessionId
         */
        $apiToken = $this->get($request, 'api-token');
        // Log::info("SessionMiddleware - RequestUrl - " . \ModStart\Core\Input\Request::currentPageUrl());
        // Log::info("SessionMiddleware - ApiToken - $apiToken");
        if (empty($apiToken) || !Session::isValidId($apiToken)) {
            $apiToken = Session::getId();
            $setApiToken = true;
        } else {
            Session::setId($apiToken);
            $sessionRestart = true;
        }
        $sessionId = Session::getId();
        // Log::info("SessionMiddleware - SessionId - $sessionId");
        if ($sessionId && $sessionId != $apiToken) {
            Session::setId($apiToken);
            $sessionRestart = true;
        }
        if ($sessionRestart) {
            Session::start();
        }
        $request->headers->set('api-token', $apiToken);

        $apiDevice = $this->get($request, 'api-device');
        $request->headers->set('api-device', $apiDevice);

        $apiParam = $this->get($request, 'api-param');
        $request->headers->set('api-param', $apiParam);

        $apiVersion = $this->get($request, 'api-version');
        $request->headers->set('api-version', $apiVersion);

        /** @var Response $response */
        $response = $next($request);
        // Log::info('SessionMiddleware - Response - ' . get_class($response));
        if ($setApiToken) {
            $illuminateResponse = 'Illuminate\Http\Response';
            $symfonyResponse = 'Symfony\Component\HttpFoundation\Response';
            if ($response instanceof $illuminateResponse) {
                $response->header('api-token', $apiToken);
            } else if ($response instanceof $symfonyResponse) {
                $response->headers->set('api-token', $apiToken);
            }
        }

        return $response;
    }


    /**
     * @param Request $request
     * @param $key
     * @return string
     *
     * 获取顺序 ( api-token, api-param, api-device )
     * 1. get/post，request 中统一使用下划线 (_)
     * 2. header，header 中统一使用下划线 (-)
     * 3. cookie，cookie 中统一使用下划线 (_)
     */
    private function get(Request &$request, $key)
    {
        $key_ = str_replace('-', '_', $key);
        $value = Input::get($key_, null);
        if (!empty($value)) return $value;
        $value = $request->header($key);
        if (!empty($value)) return $value;
        $value = $request->cookie($key_);
        if (!empty($value)) return $value;
        return null;
    }

    private function getCookieSessionId(Request &$request)
    {
        return $request->cookie(config('session.cookie'));
    }
}
