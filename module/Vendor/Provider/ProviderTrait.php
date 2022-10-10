<?php


namespace Module\Vendor\Provider;

trait ProviderTrait
{
    /**
     * @var array
     */
    private static $list = [];

    public static function register($provider)
    {
        self::$list[] = $provider;
    }

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
            usort(self::$list, function ($o1, $o2) {
                if ($o1->order() == $o2->order()) {
                    return 0;
                }
                return $o1->order() > $o2->order() ? 1 : -1;
            });
        }
        $processed = true;
        return self::$list;
    }

    private static function getByName($name)
    {
        foreach (self::all() as $item) {
            if ($item->name() == $name) {
                return $item;
            }
        }
        return null;
    }

    public static function allMap()
    {
        return array_build(self::all(), function ($k, $v) {
            return [
                $v->name(), $v->title()
            ];
        });
    }

    public static function allDefaultMap()
    {
        return array_merge(
            ['' => L('None')],
            array_build(self::all(), function ($k, $v) {
                return [
                    $v->name(), $v->title()
                ];
            })
        );
    }
}
