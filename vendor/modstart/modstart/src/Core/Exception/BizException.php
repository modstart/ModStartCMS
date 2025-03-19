<?php

namespace ModStart\Core\Exception;


use ModStart\Core\Input\Response;

class BizException extends \Exception
{
    public $param = [];

    /**
     * 抛出业务异常，系统会统一处理
     * @param $msg string 异常信息
     * @param $param array 异常参数
     * @throws BizException
     * @example
     * // 抛出异常消息
     * BizException::throws( '参数错误' );
     * // 使用自定义模板
     * BizException::throws( '参数错误', ['view' => 'theme.default.customView','viewData'=>[ 'foo'=>'bar' ]] );
     * // 使用自定义状态码，返回 404
     * BizException::throws( '参数错误', ['statusCode' => 404]);
     */
    public static function throws($msg, $param = [])
    {
        $e = new BizException($msg);
        $e->param = $param;
        throw $e;
    }

    /**
     * 条件抛出业务异常，参数参考 throws 方法
     * @param $msg string 异常信息
     * @param $condition bool 条件
     * @param $param array 异常参数
     * @throws BizException
     */
    public static function throwsIf($msg, $condition, $param = [])
    {
        if ($condition) {
            $e = new BizException($msg);
            $e->param = $param;
            throw $e;
        }
    }

    /**
     * 如果 object 为空，抛出业务异常，参数参考 throws 方法
     * @param $msg string 异常信息
     * @param $object mixed 对象
     * @param $param array
     * @throws BizException
     */
    public static function throwsIfEmpty($msg, $object, $param = [])
    {
        if (empty($object)) {
            $e = new BizException($msg);
            $e->param = $param;
            throw $e;
        }
    }

    /**
     * 如果 object 不为空，抛出业务异常，参数参考 throws 方法
     * @param $msg string 异常信息
     * @param $object mixed 对象
     * @param $param array
     * @throws BizException
     */
    public static function throwsIfNotEmpty($msg, $object, $param = [])
    {
        if (!empty($object)) {
            $e = new BizException($msg);
            $e->param = $param;
            throw $e;
        }
    }

    /**
     * 如果 response 有错误，抛出业务异常，参数参考 throws 方法
     * @param $response array 标准响应 ['code'=>0,'msg'=>'','data'=>[]]
     * @param $prefix string 异常信息前缀
     * @param $param array 异常参数
     * @throws BizException
     */
    public static function throwsIfResponseError($response, $prefix = '', $param = [])
    {
        if ($prefix) {
            $prefix = $prefix . ':';
        }
        if (empty($response)) {
            $e = new BizException($prefix . 'Response Empty');
            $e->param = $param;
            throw $e;
        }
        if ($response['code']) {
            $e = new BizException($prefix . $response['msg']);
            $e->param = $param;
            throw $e;
        }
    }

    /**
     * 如果 $e 与 $messagePatterns 匹配，抛出业务异常
     * @param $error \Exception|string 异常或错误
     * @param $messagePatterns array 异常信息模板 ['pattern'=>'message']
     * @param $messagePrefix string 异常信息前缀
     * @param $isRegex bool 是否正则匹配
     * @throws BizException
     */
    public static function throwsIfMessageMatch($error, $messagePatterns, $messagePrefix = '', $isRegex = false)
    {
        $msg = $error;
        if ($msg instanceof \Exception) {
            $msg = $msg->getMessage();
        }
        foreach ($messagePatterns as $pattern => $message) {
            if ($isRegex) {
                if (preg_match($pattern, $msg)) {
                    BizException::throws($messagePrefix . $message);
                }
            } else {
                if (strpos($msg, $pattern) !== false) {
                    BizException::throws($messagePrefix . $message);
                }
            }
        }
        throw $error;
    }

    public function toResponse()
    {
        return Response::generateError($this->getMessage());
    }

}
