<?php


namespace ModStart\Data\Event;


use ModStart\Core\Util\EventUtil;

class DataFileUploadedEvent
{
    const OPT_IMAGE_COMPRESS_IGNORE = 'imageCompressIgnore';
    const OPT_IMAGE_WATERMARK_IGNORE = 'imageWatermarkIgnore';

    public $driver;
    public $category;
    public $path;
    public $opt;

    public static function fire($driver, $category, $path, $opt = [])
    {
        $event = new DataFileUploadedEvent();
        $event->driver = $driver;
        $event->category = $category;
        $event->path = $path;
        $event->opt = $opt;
        EventUtil::fire($event);
    }

    public function getOpt($key, $defaultValue = null)
    {
        if (isset($this->opt[$key])) {
            return $this->opt[$key];
        }
        return $defaultValue;
    }

    private static $param = [];

    /**
     * @param $key
     * @param $value
     * @deprecated use $opt remove after 2023-12-01
     */
    public static function setParam($key, $value)
    {
        self::$param[$key] = $value;
    }

    /**
     * @param $key
     * @deprecated use $opt remove after 2023-12-01
     */
    public static function forgetParam($key)
    {
        unset(self::$param[$key]);
    }

    /**
     * @param $key
     * @return mixed|null
     * @deprecated use $opt remove after 2023-12-01
     */
    public static function getParam($key)
    {
        return isset(self::$param[$key]) ? self::$param[$key] : null;
    }

}
