<?php

namespace Module\AigcBase\Biz;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\SerializeUtil;
use Module\AigcBase\Job\AigcWorkProcessJob;
use Module\AigcBase\Model\AigcWork;
use Module\Vendor\Type\JobStatus;

abstract class AbstractAigcWorkBiz
{
    abstract public function name();

    abstract public function title();

    abstract public function run($work, $param = []);

    public static function submit($param)
    {
        $data = [];
        $data['biz'] = static::NAME;
        $data['status'] = JobStatus::QUEUE;
        $data['param'] = SerializeUtil::jsonEncode($param);
        $data = ModelUtil::insert(AigcWork::class, $data);
        AigcWorkProcessJob::create($data['id']);
    }

    public static function runWork($workId)
    {
        $work = ModelUtil::get(AigcWork::class, $workId);
        ModelUtil::decodeRecordJson($work, ['param', 'result']);
        $bizer = AigcWorkBiz::getByName($work['biz']);
        $bizer->run($work);
    }
}
