<?php


namespace Module\Vendor\Provider\RandomImage;


class RandomImageProvider
{
    /**
     * @return AbstractRandomImageProvider
     */
    public static function get()
    {
        static $instance = null;
        if (null === $instance) {
            $driver = config('RandomImageProvider');
            if (empty($driver)) {
                $driver = DefaultRandomImageProvider::class;
            }
            $instance = app($driver);
        }
        return $instance;
    }

    public static function getImage($biz = '', $param = [])
    {
        return self::get()->get(array_merge(['biz' => $biz], $param));
    }
}
