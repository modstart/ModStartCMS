<?php

namespace ModStart\Core\Util;

/**
 * @Util 值处理工具
 */
class ValueUtil
{
    /**
     * @Util 解析封装值，如果是回调函数则执行并返回结果，否则直接返回
     * @param $value mixed|回调函数
     * @return mixed
     */
    public static function value($value)
    {
        if (is_callable($value)) {
            return $value();
        }
        return $value;
    }
}
