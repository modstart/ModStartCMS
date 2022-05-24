<?php


namespace ModStart\Core\Hook;


use ModStart\Core\Exception\BizException;

class ModStartHook
{
    private static $listeners = [];

    /**
     * subscribe hook
     * @param $name string|array PageHeadAppend,DialogPageHeadAppend
     * @param $callable \Closure
     */
    public static function subscribe($name, $callable)
    {
        if (!is_array($name)) {
            $name = [$name];
        }
        foreach ($name as $item) {
            if (!isset(self::$listeners[$item])) {
                self::$listeners[$item] = [];
            }
            self::$listeners[$item][] = $callable;
        }
    }

    /**
     * get hook listeners
     * @param string $name
     * @return array|mixed
     */
    public static function get($name = '')
    {
        if (empty($name)) {
            return self::$listeners;
        }
        return array_key_exists($name, self::$listeners) ? self::$listeners[$name] : [];
    }

    /**
     * fire one hook and get array result
     * @param $name
     * @param null $param
     * @param null $extra
     * @return array|mixed
     */
    public static function fire($name, &$param = null, $extra = null)
    {
        $results = [];
        foreach (static::get($name) as $key => $callable) {
            $results[$key] = self::call($callable, $name, $param, $extra);
        }
        return $results;
    }

    /**
     * fire one hook and get string result
     * @param $name
     * @param null $param
     * @param null $extra
     * @return string
     */
    public static function fireInView($name, &$param = null, $extra = null)
    {
        return join('', self::fire($name, $param, $extra));
    }

    private static function call($callable, $name = '', &$param = null, $extra = null)
    {
        if ($callable instanceof \Closure) {
            $result = call_user_func_array($callable, [& $param, $extra]);
        } elseif (is_array($callable)) {
            list($callable, $method) = $callable;
            $result = call_user_func_array([&$callable, $method], [& $param, $extra]);
        } else if (is_object($callable)) {
            $method = "on$name";
            $result = call_user_func_array([&$callable, $method], [& $param, $extra]);
        } elseif (strpos($callable, '::')) {
            $result = call_user_func_array($callable, [& $param, $extra]);
        } else {
            BizException::throws('ModStartHook call error');
        }
        return $result;
    }
}
