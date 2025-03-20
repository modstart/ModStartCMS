<?php

namespace Module\AigcBase\Util;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\SerializeUtil;
use Module\AigcBase\Model\AigcWork;

class AigcWorkUtil
{
    public static function updateResult($id, $data)
    {
        $task = ModelUtil::get(AigcWork::class, $id);
        if (empty($task)) {
            return;
        }
        ModelUtil::decodeRecordJson($task, ['result']);
        $result = array_merge($task['result'], $data);
        ModelUtil::update(AigcWork::class, $id, [
            'result' => SerializeUtil::jsonEncodeObject($result)
        ]);
    }
}
