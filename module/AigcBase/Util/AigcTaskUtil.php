<?php

namespace Module\AigcBase\Util;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\SerializeUtil;
use Module\AigcBase\Model\AigcTask;

class AigcTaskUtil
{
    public static function updateResult($id, $data)
    {
        $task = ModelUtil::get(AigcTask::class, $id);
        if (empty($task)) {
            return;
        }
        ModelUtil::decodeRecordJson($task, ['result']);
        $result = array_merge($task['result'], $data);
        ModelUtil::update(AigcTask::class, $id, [
            'result' => SerializeUtil::jsonEncode($result)
        ]);
    }

    public static function updateResultMsg($id, $msg)
    {
        self::updateResult($id, ['msg' => $msg]);
    }
}
