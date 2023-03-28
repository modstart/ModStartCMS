<?php


namespace ModStart\Data\Event;


use Illuminate\Support\Facades\Event;
use ModStart\Core\Util\EventUtil;

/**
 * 用户上传文件完成事件
 * 和 DataFileUploadedEvent 的区别为一个纯文件内容，一个是用户请求行为
 * Class DataUploadedEvent
 * @package ModStart\Data\Event
 */
class DataUploadedEvent
{
    public $uploadTable;
    public $userId;
    public $category;
    public $dataId;

    public static function fire($uploadTable, $userId, $category, $dataId)
    {
        $event = new static();
        $event->uploadTable = $uploadTable;
        $event->userId = $userId;
        $event->category = $category;
        $event->dataId = $dataId;
        EventUtil::fire($event);
    }

    public static function listen($uploadTable, $callback)
    {
        Event::listen(DataUploadedEvent::class, function (DataUploadedEvent $event) use ($uploadTable, $callback) {
            if ($event->uploadTable == $uploadTable) {
                call_user_func($callback, $event);
            }
        });
    }
}
