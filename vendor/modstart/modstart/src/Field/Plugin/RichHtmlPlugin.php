<?php


namespace ModStart\Field\Plugin;


class RichHtmlPlugin
{
    /**
     * @var AbstractRichHtmlPlugin[]
     */
    private static $list = [];

    public static function reigster($plugin)
    {
        self::$list[] = $plugin;
    }

    /**
     * @return AbstractRichHtmlPlugin[]
     */
    public static function all()
    {
        foreach (self::$list as $k => $plugin) {
            if (is_string($plugin)) {
                self::$list[$k] = app($plugin);
            }
        }
        return self::$list;
    }

}
