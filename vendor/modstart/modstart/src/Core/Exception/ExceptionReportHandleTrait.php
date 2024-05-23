<?php

namespace ModStart\Core\Exception;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\View\View;
use ModStart\Core\Input\Response;
use ModStart\Core\Monitor\StatisticMonitor;
use ModStart\Core\Util\CurlUtil;
use ModStart\Core\Util\SerializeUtil;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait ExceptionReportHandleTrait
{
    private function errorReportCheck($exception)
    {
        try {
            $errorReportUrl = config('env.ERROR_REPORT_URL', null);
            if (empty($errorReportUrl)) {
                return;
            }
            $needReport = true;
            if ($needReport && $exception instanceof BizException) {
                $needReport = false;
            }
            if ($needReport && $exception instanceof NotFoundHttpException) {
                $needReport = false;
            }
            if ($needReport && $exception instanceof MethodNotAllowedHttpException) {
                $needReport = false;
            }
            if ($needReport && $exception instanceof HttpException) {
                switch ($exception->getStatusCode()) {
                    case 200:
                        $needReport = false;
                        break;
                }
            }
            if ($needReport && $this->isExceptionIgnore($exception)) {
                $needReport = false;
            }
            if (!$needReport) {
                return;
            }
            $error = [];
            $error['URL'] = Request::url();
            if (in_array($error['URL'], [
                'http://localhost',
            ])) {
                $error['URL'] = base_path();
                $error['HOST'] = gethostname();
            }
            $error['METHOD'] = 'GET';
            if (\ModStart\Core\Input\Request::isPost()) {
                $error['METHOD'] = 'POST';
            }
            $error['FILE'] = $exception->getFile() . ':' . $exception->getLine();
            $error['MESSAGE'] = $exception->getMessage();
            foreach ($error as &$v) {
                $v = str_replace(base_path(), '', $v);
            }
            CurlUtil::get($errorReportUrl, ['data' => SerializeUtil::jsonEncode($error)]);
        } catch (\Exception $e) {
            // do nothing
        }
    }

    private function isExceptionIgnore($exception)
    {
        if ($exception instanceof \UnexpectedValueException) {
            if (Str::contains($exception->getMessage(), 'Invalid method override')) {
                return true;
            }
        }
        return false;
    }

    private function getExceptionResponse($exception)
    {
        if ($exception instanceof BizException) {
            $exceptionParam = $exception->param;
            $ret = Response::sendError($exception->getMessage());
            $statusCode = 200;
            if (!\ModStart\Core\Input\Request::isAjax()) {
                if (empty($exceptionParam['view'])
                    && !empty($exceptionParam['statusCode'])
                    && in_array($exceptionParam['statusCode'], [403, 404, 500])) {
                    $exceptionParam['view'] = 'modstart::core.msg.' . $exceptionParam['statusCode'];
                    $statusCode = $exceptionParam['statusCode'];
                }
                if (!empty($exceptionParam['view'])) {
                    if (!isset($exceptionParam['viewData'])) {
                        $exceptionParam['viewData'] = [];
                    }
                    $ret = view($exceptionParam['view'], array_merge([
                        '_viewFrame' => 'theme.default.pc.frame',
                        'msg' => $exception->getMessage(),
                    ], $exceptionParam['viewData']));
                }
            }
            if ($ret instanceof View) {
                return response()->make($ret, $statusCode);
            }
            return $ret;
        } else if ($exception instanceof MethodNotAllowedHttpException) {
            return Response::page404();
        } elseif ($exception instanceof NotFoundHttpException) {
            if (\ModStart\Core\Input\Request::isAjax()) {
                return Response::page404();
            }
            return null;
        } elseif ($exception instanceof ModelNotFoundException) {
            return null;
        } else if ($exception instanceof HttpException) {
            switch ($exception->getStatusCode()) {
                case 200:
                    if (\ModStart\Core\Input\Request::isJsonp()) {
                        $ret = Response::generateError($exception->getMessage());
                        return Response::jsonp($ret);
                    }
                    $ret = Response::sendError($exception->getMessage());
                    if ($ret instanceof View) {
                        return response()->make($ret);
                    }
                    return $ret;
            }
        } else if ($exception instanceof \UnexpectedValueException) {
            if (Str::contains($exception->getMessage(), 'Invalid method override')) {
                return Response::page404();
            }
        }

        try {
            if (StatisticMonitor::isEnable()) {
                $request = \ModStart\Core\Input\Request::instance();
                $time = round((microtime(true) - LARAVEL_START) * 1000, 2);
                $url = $request->url();
                $method = $request->method();
                $routeAction = Route::currentRouteAction();
                $domain = \ModStart\Core\Input\Request::domain();
                StatisticMonitor::tick($domain, "$method." . $routeAction, $time, false, 500);
            }
        } catch (\Exception $e) {
        }

        return null;
    }

}
