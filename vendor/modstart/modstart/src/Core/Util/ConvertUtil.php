<?php


namespace ModStart\Core\Util;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Renderable;

class ConvertUtil
{
    /**
     * 把给定的值转化为数组.
     *
     * @param $value
     * @param bool $filter
     *
     * @return array
     */
    public static function toArray($value, $filter = true)
    {
        if (!$value) {
            return [];
        }

        if ($value instanceof \Closure) {
            $value = $value();
        }

        if (is_array($value)) {
        } elseif ($value instanceof Jsonable) {
            $value = json_decode($value->toJson(), true);
        } elseif ($value instanceof Arrayable) {
            $value = $value->toArray();
        } elseif (is_string($value)) {
            $array = null;
            try {
                $array = json_decode($value, true);
            } catch (\Throwable $e) {
            }
            $value = is_array($array) ? $array : explode(',', $value);
        } else {
            $value = (array)$value;
        }
        return $filter ? array_filter($value, function ($v) {
            return $v !== '' && $v !== null;
        }) : $value;
    }

    /**
     * 把给定的值渲染为字符串
     *
     * @param string|Grid|\Closure|Renderable|Htmlable $value
     * @param array $params
     * @param object $newThis
     *
     * @return string
     */
    public static function render($value, $params = [], $newThis = null)
    {
        if (is_string($value)) {
            return $value;
        }
        if ($value instanceof \Closure) {
            $newThis && ($value = $value->bindTo($newThis));
            $value = $value(...(array)$params);
        }
        if ($value instanceof Renderable) {
            return (string)$value->render();
        }
        if ($value instanceof Htmlable) {
            return (string)$value->toHtml();
        }
        return (string)$value;
    }
}