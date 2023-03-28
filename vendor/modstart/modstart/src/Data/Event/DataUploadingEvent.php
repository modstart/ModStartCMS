<?php


namespace ModStart\Data\Event;


use Illuminate\Support\Facades\Event;
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

    public static function fire($uploadTable, $userId, $category)
    {
        $event = new static();
        $event->uploadTable = $uploadTable;
        $event->userId = $userId;
        $event->category = $category;
        EventUtil::fire($event);
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
