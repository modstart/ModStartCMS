<?php


namespace Module\Vendor\Html;


class HtmlConvertUtil
{
    public static function callInterceptors($interceptors, $value)
    {
        if (empty($interceptors)) {
            return $value;
        }
        if (!is_array($interceptors)) {
            $interceptors = [$interceptors];
        }
        foreach ($interceptors as $interceptor) {
            /** @var $instance HtmlConverterInterceptor */
            $instance = app($interceptor);
            $value = $instance->convert($value);
        }
        return $value;
    }
}
