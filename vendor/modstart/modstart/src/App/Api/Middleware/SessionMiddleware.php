<?php


namespace ModStart\App\Api\Middleware;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;


class SessionMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        $setApiToken = false;
        $sessionRestart = false;
        
        $apiToken = $this->get($request, 'api-token');
                        if (empty($apiToken) || !Session::isValidId($apiToken)) {
            $apiToken = Session::getId();
            $setApiToken = true;
        } else {
            Session::setId($apiToken);
            $sessionRestart = true;
        }
        $sessionId = Session::getId();
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

        
        $response = $next($request);
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
