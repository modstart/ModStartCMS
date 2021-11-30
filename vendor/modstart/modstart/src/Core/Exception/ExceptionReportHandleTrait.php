<?php

namespace ModStart\Core\Exception;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CurlUtil;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait ExceptionReportHandleTrait
{
    private function errorReportCheck($exception)
    {
        try {
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
            if ($needReport && $exception instanceof \UnexpectedValueException) {
                if (Str::contains($exception->getMessage(), 'Invalid method override')) {
                    $needReport = false;
                }
            }
            if ($needReport) {
                $errorReportUrl = config('env.ERROR_REPORT_URL', null);
                if ($errorReportUrl) {
                    $error = [];
                    $error['url'] = Request::url();
                    $error['file'] = $exception->getFile() . ':' . $exception->getLine();
                    $error['message'] = $exception->getMessage();
                    foreach ($error as &$v) {
                        $v = str_replace(base_path(), '', $v);
                    }
                    CurlUtil::get($errorReportUrl, ['data' => json_encode($error)]);
                }
            }
        } catch (\Exception $e) {
            // do nothing
        }
    }

    private function getExceptionResponse($exception)
    {
        if ($exception instanceof BizException) {
            $ret = Response::sendError($exception->getMessage());
            if ($ret instanceof View) {
                return response()->make($ret);
            }
            return $ret;
        } elseif ($exception instanceof ModelNotFoundException) {
            return null;
        }
        return null;
    }
}
