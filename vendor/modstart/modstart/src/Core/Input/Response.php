<?php

namespace ModStart\Core\Input;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\FileUtil;
use ModStart\Core\Util\SerializeUtil;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Response
{
    /**
     * 从另外一个接口返回data数据
     * 如果ret是一个标准错误会抛出BizException异常，该异常会被同一捕获
     * 如果ret是一个JsonResponse，会尝试获取实际值
     *
     * @param $ret
     * @param $key
     * @return mixed
     * @throws BizException
     */
    public static function tryGetData($ret, $key = null)
    {
        if ($ret instanceof JsonResponse) {
            $ret = $ret->getData(true);
        }
        if (!isset($ret['code'])) {
            BizException::throws('ERROR_RESPONSE');
        }
        if ($ret['code']) {
            BizException::throws($ret['msg']);
        }
        if (null !== $key) {
            if (!array_key_exists($key, $ret['data'])) {
                BizException::throws('data not exists ' . $key);
            }
            return $ret['data'][$key];
        }
        return isset($ret['data']) ? $ret['data'] : null;
    }

    public static function onSuccess($result, $successCallback, $errorCallback = null)
    {
        if (self::isSuccess($result)) {
            call_user_func($successCallback, $result);
        } else {
            if ($errorCallback) {
                call_user_func($errorCallback, $result);
            }
        }
    }

    public static function isSuccess($result)
    {
        if ($result instanceof JsonResponse) {
            $result = $result->getData(true);
        }
        return (isset($result['code']) && 0 === $result['code']);
    }

    public static function isError($result)
    {
        return !self::isSuccess($result);
    }

    /**
     * 需要独立处理的返回结果，原始返回结果
     * @param $result
     * @return bool
     */
    public static function isRaw($result)
    {
        if ($result instanceof \Illuminate\Http\Response) {
            return true;
        }
        return false;
    }

    public static function generateRedirect($redirect)
    {
        return self::generate(-1, null, null, $redirect);
    }

    public static function generate($code, $msg, $data = null, $redirect = null)
    {
        if (null === $redirect) {
            if ($_redirect = trim(Input::get('_redirect'))) {
                $redirect = $_redirect;
            }
        }
        $response = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
            'redirect' => $redirect,
        ];
        if (null === $data) {
            unset($response['data']);
        }
        if (null === $redirect) {
            unset($response['redirect']);
        }
        return $response;
    }

    public static function generateSuccessPaginate($page, $pageSize, $paginateData)
    {
        return self::generateSuccessPaginateData($page, $pageSize, $paginateData['records'], $paginateData['total']);
    }

    public static function generateSuccessPaginateData($page, $pageSize, $records, $total, $maxRecords = -1)
    {
        return self::generateSuccessData([
            'page' => $page,
            'pageSize' => $pageSize,
            'records' => $records,
            'total' => $total,
            'maxRecords' => $maxRecords,
        ]);
    }

    public static function generateSuccessData($data)
    {
        return self::generate(0, 'ok', $data);
    }

    public static function generateSuccess($msg = 'ok')
    {
        return self::generate(0, $msg);
    }

    public static function generateError($msg = 'error', $data = null, $redirect = null)
    {
        return self::generate(-1, $msg, $data, $redirect);
    }

    public static function jsonSuccessData($data)
    {
        return self::json(0, 'ok', $data);
    }

    public static function jsonSuccess($msg = 'ok')
    {
        return self::json(0, $msg);
    }

    public static function jsonError($msg = 'error')
    {
        return self::json(-1, $msg);
    }

    public static function jsonException(\Exception $e)
    {
        return self::jsonError($e->getMessage());
    }

    public static function json($code, $msg, $data = null, $redirect = null)
    {
        $response = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
            'redirect' => $redirect,
        ];
        if (null === $redirect) {
            unset($response['redirect']);
        }
        return \Illuminate\Support\Facades\Response::json($response);
    }

    public static function jsonFromGenerate($ret)
    {
        if ($ret instanceof JsonResponse) {
            return $ret;
        }
        if ($ret['code']) {
            return self::json($ret['code'], $ret['msg'], isset($ret['data']) ? $ret['data'] : null, isset($ret['redirect']) ? $ret['redirect'] : null);
        }
        if (!isset($ret['msg'])) {
            $ret['msg'] = null;
        }
        return self::json($ret['code'], $ret['msg'], isset($ret['data']) ? $ret['data'] : null, isset($ret['redirect']) ? $ret['redirect'] : null);
    }

    public static function jsonIfGenerateSuccess($ret, $msg = 'ok', $data = null, $redirect = null)
    {
        if ($ret['code']) {
            return self::json($ret['code'], $ret['msg'], empty($ret['data']) ? null : $ret['data'], empty($ret['redirect']) ? null : $ret['redirect']);
        }
        return self::json(0, $msg, $data, $redirect);
    }

    public static function jsonRaw($data)
    {
        return \Illuminate\Support\Facades\Response::json($data);
    }

    public static function jsonp($data, $callback = null)
    {
        if (empty($callback)) {
            $callback = \Illuminate\Support\Facades\Input::get('callback', null);
        }
        if (empty($callback)) {
            return \Illuminate\Support\Facades\Response::json($data);
        }
        if (!preg_match('/^[a-zA-Z_0-9]+$/', $callback)) {
            return \Illuminate\Support\Facades\Response::json([
                'code' => -1,
                'msg' => 'callback error',
            ]);
        }
        return \Illuminate\Support\Facades\Response::jsonp($callback, $data);
    }

    public static function sendIfGenerateSuccess($ret, $msg = 'ok', $data = null, $redirect = null)
    {
        if ($ret['code']) {
            return self::send($ret['code'], $ret['msg'], empty($ret['data']) ? null : $ret['data'], empty($ret['redirect']) ? null : $ret['redirect']);
        }
        return self::send(0, $msg, $data, $redirect);
    }

    public static function sendFromGenerate($ret)
    {
        return self::send($ret['code'], empty($ret['msg']) ? null : $ret['msg'], empty($ret['data']) ? null : $ret['data'], empty($ret['redirect']) ? null : $ret['redirect']);
    }

    public static function sendError($msg, $data = null, $redirect = null)
    {
        return self::send(-1, $msg, $data, $redirect);
    }

    public static function sendException(\Exception $e)
    {
        return self::sendError($e->getMessage());
    }

    /**
     * 中断操作，会停止程序执行并输出提示信息，无返回
     * @param $msg
     * @return void
     */
    public static function abortMsg($msg)
    {
        abort(200, $msg);
    }

    /**
     * 返回一个404页面
     * @return void|JsonResponse
     */
    public static function page404()
    {
        if (Request::isAjax()) {
            return self::json(-1, L('Api Not Found : %s', Request::basePath()));
        } else {
            abort(404, L('Page Not Found'));
        }
    }

    /**
     * 页面无权限
     * @param null $msg
     * @return void|JsonResponse
     */
    public static function pagePermissionDenied($msg = null)
    {
        if (Request::isAjax()) {
            return self::json(-1, $msg ? $msg : L('No Permission'));
        } else {
            abort(403, $msg ? $msg : L('No Permission'));
        }
    }

    public static function quit($code, $msg, $data = null, $redirect = null)
    {
        $response = [
            'code' => $code,
            'msg' => $msg,
            'redirect' => $redirect,
            'data' => $data
        ];
        if (null === $redirect) {
            unset($response['redirect']);
        }
        if (\Illuminate\Support\Facades\Request::ajax()) {
            header('Content-Type: application/json; charset=UTF-8');
            echo SerializeUtil::jsonEncode($response);
        } else {
            header('Content-Type: text/html; charset=UTF-8');
            echo View::make('modstart::core.msg.msg', $response)->render();
        }
        exit();
    }

    public static function redirect($redirect)
    {
        return self::send(0, null, null, $redirect);
    }

    public static function redirectPermanently($redirect)
    {
        return app('redirect')->away($redirect, 301);
    }

    public static function send($code, $msg, $data = null, $redirect = null)
    {
        if (\Illuminate\Support\Facades\Request::ajax()
            || Request::headerGet('is-ajax', false)
            || (($headerAccept = Request::headerGet('accept')) && $headerAccept == 'application/json')
        ) {
            return self::json($code, $msg, $data, $redirect);
        } else {
            if (empty($msg) && $redirect) {
                return app('redirect')->away($redirect);
            }
            $response = [
                'code' => $code,
                'msg' => $msg,
                'redirect' => $redirect,
                'data' => $data
            ];
            if (null === $redirect) {
                unset($response['redirect']);
            }
            return view('modstart::core.msg.msg', $response);
        }
    }

    public static function download($filename, $content, $headers = [], $filenameFallback = null)
    {
        if (empty($filenameFallback)) {
            $filenameFallback = 'file.' . FileUtil::extension($filename);
        }
        $fileNameInvalidChars = ['/'];
        $filename = str_replace($fileNameInvalidChars, '_', $filename);
        $filenameFallback = str_replace($fileNameInvalidChars, '_', $filenameFallback);
        $response = new \Illuminate\Http\Response($content);
        $disposition = $response->headers->makeDisposition(
            \Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename,
            $filenameFallback
        );
        $response->headers->set('Content-Disposition', $disposition);
        // 已知部分浏览器（QQ手机浏览器）不设置Content-Type，会导致下载文件失败
        if (!isset($headers['Content-Type'])) {
            $response->headers->set('Content-Type', 'application/octet-stream');
        }
        foreach ($headers as $k => $v) {
            $response->headers->set($k, $v);
        }
        return $response;
    }

    public static function raw($content, $headers = [])
    {
        $response = new \Illuminate\Http\Response($content);
        foreach ($headers as $k => $v) {
            $response->headers->set($k, $v);
        }
        return $response;
    }

    public static function textEventStreamed($callback)
    {
        $response = new StreamedResponse();
        $response->setCallback(function () use (&$callback) {
            // {"type":"data","data":...}
            // {"type":"error","data":...}
            // {"type":"end"}
            $sendCallback = function ($type, $data = null) {
                $payload = [];
                $payload['type'] = $type;
                if (!is_null($data)) {
                    $payload['data'] = $data;
                }
                echo "data: " . json_encode($payload) . "\n\n";
                ob_flush();
                flush();
            };
            $dataCallback = function ($data) use (&$sendCallback) {
                call_user_func($sendCallback, 'data', $data);
            };
            $endCallBack = function () use (&$sendCallback) {
                call_user_func($sendCallback, 'end');
            };
            $errorCallback = function ($msg) use (&$sendCallback) {
                call_user_func($sendCallback, 'error', $msg);
            };
            try {
                call_user_func_array($callback, [$sendCallback, [
                    'dataCallback' => $dataCallback,
                    'endCallBack' => $endCallBack,
                    'errorCallback' => $errorCallback,
                ]]);
            } catch (BizException $e) {
                call_user_func($errorCallback, $e->getMessage());
            } catch (\Exception $e) {
                throw $e;
            }
        });
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('X-Accel-Buffering', 'no');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->send();
    }
}
