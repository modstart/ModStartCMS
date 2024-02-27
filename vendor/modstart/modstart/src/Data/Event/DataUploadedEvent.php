<?php


namespace ModStart\Data\Event;


use Illuminate\Support\Facades\Event;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Core\Util\EventUtil;

/**
 * 用户文件上传完事件
 * Class DataUploadedEvent
 * @package ModStart\Data\Event
 *
 * 一些区别
 * DataUploadedEvent 是文件上传完毕后的事件，包含上传的表、用户ID等信息等
 * DataFileUploadedEvent 是文件上传完毕后的事件，只包含文件路径等纯物理文件信息
 */
class DataUploadedEvent
{
    /**
     * 上传参数
     * userType 用户类型 admin,member
     * userId 用户ID
     */
    const OPT_PARAM = 'param';

    public $uploadTable;
    public $userId;
    public $category;
    public $dataId;
    public $opt;

    public static function fire($uploadTable, $userId, $category, $dataId, $opt = [])
    {
        $event = new static();
        $event->uploadTable = $uploadTable;
        $event->userId = $userId;
        $event->category = $category;
        $event->dataId = $dataId;
        $event->opt = $opt;
        EventUtil::fire($event);
    }

    public function getOpt($key, $defaultValue = null)
    {
        return ArrayUtil::getByDotKey($this->opt, $key, $defaultValue);
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
