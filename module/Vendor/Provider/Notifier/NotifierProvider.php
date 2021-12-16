<?php


namespace Module\Vendor\Provider\Notifier;


class NotifierProvider
{
    /**
     * @return AbstractNotifierProvider[]
     */
    public static function get()
    {
        static $instances = null;
        if (null === $instances) {
            $drivers = config('NotifierProviders');
            if (empty($drivers)) {
                $drivers = [
                    DefaultNotifierProvider::class
                ];
            }
            $instances = array_map(function ($driver) {
                return app($driver);
            }, array_unique($drivers));
        }
        return $instances;
    }

    public static function notify($biz, $title, $content, $param = [])
    {
        foreach (self::get() as $instance) {
            $instance->notify($biz, $title, $content, $param);
        }
    }
}
