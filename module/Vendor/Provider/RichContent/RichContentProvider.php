<?php


namespace Module\Vendor\Provider\RichContent;


use Module\Vendor\Provider\RichContent\AbstractRichContentProvider;
use Module\Vendor\Provider\RichContent\UEditorRichContentProvider;

class RichContentProvider
{
    /**
     * @var AbstractRichContentProvider[]
     */
    private static $instances = [
        UEditorRichContentProvider::class,
    ];

    public static function register($provider)
    {
        self::$instances[] = $provider;
    }

    public static function all()
    {
        foreach (self::$instances as $k => $v) {
            if ($v instanceof \Closure) {
                self::$instances[$k] = call_user_func($v);
            } else if (is_string($v)) {
                self::$instances[$k] = app($v);
            }
        }
        return self::$instances;
    }

    public static function getByName($name)
    {
        foreach (self::all() as $instance) {
            if ($instance->name() == $name) {
                return $instance;
            }
        }
        return null;
    }
}
