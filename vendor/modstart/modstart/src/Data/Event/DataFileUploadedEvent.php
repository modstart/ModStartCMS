<?php


namespace ModStart\Data\Event;


use ModStart\Core\Util\ArrayUtil;
use ModStart\Core\Util\EventUtil;

/**
 * 文件上传完毕信息
 * Class DataFileUploadedEvent
 * @package ModStart\Data\Event
 *
 * 一些区别
 * DataUploadedEvent 是文件上传完毕后的事件，包含上传的表、用户ID等信息等
 * DataFileUploadedEvent 是文件上传完毕后的事件，只包含文件路径等纯物理文件信息
 */
class DataFileUploadedEvent
{
    /**
     * 图片压缩忽略
     */
    const OPT_IMAGE_COMPRESS_IGNORE = 'imageCompressIgnore';
    /**
     * 图片水印忽略
     */
    const OPT_IMAGE_WATERMARK_IGNORE = 'imageWatermarkIgnore';
    /**
     * 上传参数
     * userType 用户类型 admin,member
     * userId 用户ID
     */
    const OPT_PARAM = 'param';

    public $driver;
    public $category;
    public $path;
    public $opt;

    public static function fire($driver, $category, $path, $opt = [])
    {
        $event = new static();
        $event->driver = $driver;
        $event->category = $category;
        $event->path = $path;
        $event->opt = $opt;
        EventUtil::fire($event);
    }

    public function getOpt($key, $defaultValue = null)
    {
        return ArrayUtil::getByDotKey($this->opt, $key, $defaultValue);
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
