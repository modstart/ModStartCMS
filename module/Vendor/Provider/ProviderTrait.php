<?php


namespace Module\Vendor\Provider;

trait ProviderTrait
{
    /**
     * @var array
     */
    private static $list = [];

    /**
     * 注册
     * @param $provider
     */
    public static function register($provider)
    {
        self::$list[] = $provider;
    }

    /**
     * 列出全部
     * @return array
     */
    public static function listAll()
    {
        static $processed = false;
        if ($processed) {
            return self::$list;
        }
        $hasOrder = false;
        foreach (self::$list as $k => $v) {
            if ($v instanceof \Closure) {
                self::$list[$k] = call_user_func($v);
            } else if (is_string($v)) {
                self::$list[$k] = app($v);
            }
            if (!$hasOrder && method_exists(self::$list[$k], 'order')) {
                $hasOrder = true;
            }
        }
        if ($hasOrder) {
            @usort(self::$list, function ($o1, $o2) {
                if ($o1->order() == $o2->order()) {
                    return 0;
                }
                return $o1->order() > $o2->order() ? 1 : -1;
            });
        }
        $processed = true;
        return self::$list;
    }

    /**
     * 列出全部Map name->title
     * @return array
     */
    public static function allMap()
    {
        return array_build(self::listAll(), function ($k, $v) {
            return [
                $v->name(), $v->title()
            ];
        });
    }

    /**
     * 列出全部Map（包含一个空）name->title
     * @return array|string[]
     */
    public static function allDefaultMap()
    {
        return array_merge(
            ['' => L('None')],
            array_build(self::listAll(), function ($k, $v) {
                return [
                    $v->name(), $v->title()
                ];
            })
        );
    }

    /**
     * 判断是否为空
     * @return bool
     */
    public static function isEmpty()
    {
        return empty(self::$list);
    }

    public static function getByName($name)
    {
        foreach (self::listAll() as $item) {
            if ($item->name() == $name) {
                return $item;
            }
        }
        return null;
    }

    public static function first()
    {
        foreach (self::listAll() as $item) {
            return $item;
        }
        return null;
    }
}
