<?php

namespace Module\AigcBase\Job;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Job\BaseJob;
use ModStart\Core\Util\LogUtil;
use ModStart\Core\Util\StrUtil;
use Module\AigcBase\Biz\AigcWorkBiz;
use Module\AigcBase\Model\AigcWork;
use Module\AigcBase\Util\AigcWorkUtil;
use Module\Vendor\Type\JobStatus;

class AigcWorkProcessJob extends BaseJob
{
    public $workId;

    public static function create($workId, $delay = 0)
    {
        $job = new static();
        $job->workId = $workId;
        if ($delay) {
            $job->delay($delay);
        }
        $job->onQueue('Aigc');
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }

    private function markFail($msg)
    {
        LogUtil::info('AigcWorkProcessJob.fail - ' . $this->workId, $msg);
        ModelUtil::update(AigcWork::class, $this->workId, [
            'status' => JobStatus::FAIL,
            'statusRemark' => StrUtil::mbLimit($msg, 100),
        ]);
    }

    public function handle()
    {
        LogUtil::info('AigcWorkProcessJob.start - ' . $this->workId);
        $work = ModelUtil::get(AigcWork::class, $this->workId);
        if (empty($work)) {
            LogUtil::info('AigcWorkProcessJob.empty - ' . $this->workId);
            return;
        }
        if ($work['status'] != JobStatus::QUEUE) {
            LogUtil::info('AigcWorkProcessJob.done - ' . $this->workId);
            return;
        }
        ModelUtil::update(AigcWork::class, $this->workId, [
            'status' => JobStatus::PROCESS,
            'startTime' => date('Y-m-d H:i:s'),
        ]);
        ModelUtil::transactionCommit();
        ModelUtil::decodeRecordJson($work, ['param', 'result']);
        $bizer = AigcWorkBiz::getByName($work['biz']);
        if (empty($bizer)) {
            $this->markFail('bizer.empty');
            return;
        }

        try {
            $ret = $bizer->run($work, []);
        } catch (BizException $e) {
            $ret = Response::generateError($e->getMessage());
        } catch (\Exception $e) {
            throw $e;
        }
        LogUtil::info('AigcWorkProcessJob.result - ' . $this->workId, $ret);
        if (Response::isError($ret)) {
            $this->markFail('callQueue.error - ' . $ret['msg']);
            return;
        }
        if (empty($ret['data']['result'])) {
            $ret['data']['result'] = [];
        }
        ModelUtil::update(AigcWork::class, $this->workId, [
            'status' => JobStatus::SUCCESS,
            'cost' => time() - strtotime($work['startTime']),
        ]);
        AigcWorkUtil::updateResult($this->workId, $ret['data']['result']);
        LogUtil::info('AigcWorkProcessJob.success - ' . $this->workId, $ret);
    }
}
