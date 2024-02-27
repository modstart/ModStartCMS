<?php


namespace ModStart\Data\Event;


use Illuminate\Support\Facades\Event;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Core\Util\EventUtil;

/**
 * 用户即将上传文件事件
 * 通常用于存储空间检查等，可在事件抛出异常阻止文件上传
 * Class DataUploadingEvent
 * @package ModStart\Data\Event
 */
class DataUploadingEvent
{
    public $uploadTable;
    public $userId;
    public $category;
    public $opt;
    /**
     * 上传参数
     * userType 用户类型 admin,member
     * userId 用户ID
     */
    const OPT_PARAM = 'param';

    public static function fire($uploadTable, $userId, $category, $opt = [])
    {
        $event = new static();
        $event->uploadTable = $uploadTable;
        $event->userId = $userId;
        $event->category = $category;
        $event->opt = $opt;
        EventUtil::fire($event);
    }

    public function getOpt($key, $defaultValue = null)
    {
        return ArrayUtil::getByDotKey($this->opt, $key, $defaultValue);
    }

    public static function listen($uploadTable, $callback)
    {
        Event::listen(DataUploadingEvent::class, function (DataUploadingEvent $event) use ($uploadTable, $callback) {
            if ($event->uploadTable == $uploadTable) {
                call_user_func($callback, $event);
            }
        });
    }
}
