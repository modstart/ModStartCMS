<?php

namespace Module\AigcBase\Job;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Job\BaseJob;
use ModStart\Core\Util\LogUtil;
use ModStart\Core\Util\SerializeUtil;
use ModStart\Core\Util\StrUtil;
use Module\AigcBase\Biz\AigcTaskBiz;
use Module\AigcBase\Model\AigcTask;
use Module\AigcBase\Util\AigcTaskUtil;
use Module\Member\Util\MemberCreditUtil;
use Module\Vendor\Type\JobStatus;

class AigcTaskProcessJob extends BaseJob
{
    public $taskId;

    public static function create($taskId, $delay = 0)
    {
        $job = new static();
        $job->taskId = $taskId;
        if ($delay) {
            $job->delay($delay);
        }
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }

    private function markFail($msg, $updateResultMsg = false)
    {
        LogUtil::info('AigcTaskProcessJob.fail - ' . $this->taskId, $msg);
        ModelUtil::update(AigcTask::class, $this->taskId, [
            'status' => JobStatus::FAIL,
            'statusRemark' => StrUtil::mbLimit($msg, 100),
        ]);
        if ($updateResultMsg) {
            AigcTaskUtil::updateResultMsg($this->taskId, $msg);
        }
    }

    public function handle()
    {
        LogUtil::info('AigcTaskProcessJob.start - ' . $this->taskId);
        $task = ModelUtil::get(AigcTask::class, $this->taskId);
        if (empty($task)) {
            LogUtil::info('AigcTaskProcessJob.empty - ' . $this->taskId);
            return;
        }
        if ($task['status'] == JobStatus::SUCCESS || $task['status'] == JobStatus::FAIL) {
            LogUtil::info('AigcTaskProcessJob.done - ' . $this->taskId);
            return;
        }
        ModelUtil::transactionCommit();
        ModelUtil::decodeRecordJson($task, ['modelConfig', 'result']);
        $bizer = AigcTaskBiz::getByName($task['biz']);
        if (empty($bizer)) {
            $this->markFail('bizer.empty');
            return;
        }
        $modelConfig = $task['modelConfig'];
        if ($task['status'] == JobStatus::QUEUE) {
            LogUtil::info('AigcTaskProcessJob.queue - ' . $this->taskId);
            ModelUtil::update(AigcTask::class, $this->taskId, [
                'status' => JobStatus::PROCESS,
                'startTime' => date('Y-m-d H:i:s'),
            ]);
            if (MemberCreditUtil::getTotal($task['memberUserId']) <= 0) {
                $this->markFail(modstart_module_config('Member', 'creditName', '积分') . '不足', true);
                return;
            }
            try {
                $ret = $bizer->callQueue($modelConfig, [
                    'task' => $task,
                ]);
                LogUtil::info('AigcTaskProcessJob.queueRet - ' . $this->taskId, $ret);
            } catch (BizException $e) {
                $ret = Response::generateError($e->getMessage());
            } catch (\Exception $e) {
                throw $e;
            }
            if (Response::isError($ret)) {
                $this->markFail('callQueue.error - ' . $ret['msg']);
                return;
            }
            if (empty($ret['data']['result'])) {
                $this->markFail('callQueue.result.empty');
                return;
            }
            AigcTaskUtil::updateResult($this->taskId, $ret['data']['result']);
            self::create($this->taskId, 20);
        } else {
            if (empty($task['result'])) {
                $this->markFail('result.empty');
                return;
            }
            try {
                $ret = $bizer->callQuery($task['result'], [
                    'task' => $task,
                ]);
                LogUtil::info('AigcTaskProcessJob.queryRet - ' . $this->taskId, $ret);
            } catch (BizException $e) {
                $ret = Response::generateError($e->getMessage());
            } catch (\Exception $e) {
                throw $e;
            }
            if (Response::isError($ret)) {
                $this->markFail('callQuery.error - ' . $ret['msg']);
                return;
            }
            if (empty($ret['data']['status'])) {
                $this->markFail('callQuery.status.empty');
                return;
            }
            if ($ret['data']['status'] == 'SUCCESS') {
                LogUtil::info('AigcTaskProcessJob.success - ' . $this->taskId, $ret);
                if (empty($ret['data']['result'])) {
                    $this->markFail('callQuery.result.empty');
                    return;
                }
                $result = array_merge($task['result'], $ret['data']['result']);
                $update = [];
                $update['status'] = JobStatus::SUCCESS;
                $update['cost'] = time() - strtotime($task['startTime']);
                $update['result'] = SerializeUtil::jsonEncode($result);
                ModelUtil::update(AigcTask::class, $this->taskId, $update);
                foreach ($update as $k => $v) {
                    $task[$k] = $v;
                }
                $creditCost = $bizer->creditCost($result, [
                    'task' => $task,
                ]);
                LogUtil::info('AigcTaskProcessJob.creditCost - ' . $this->taskId, $creditCost);
                if ($creditCost > 0 && $task['creditCost'] != $creditCost) {
                    ModelUtil::transactionBegin();
                    $task = ModelUtil::getWithLock(AigcTask::class, [
                        'id' => $task['id'],
                        'creditCost' => $task['creditCost'],
                    ]);
                    if (!empty($task)) {
                        ModelUtil::update(
                            AigcTask::class,
                            $task['id'],
                            [
                                'creditCost' => $creditCost,
                            ]
                        );
                        MemberCreditUtil::change(
                            $task['memberUserId'],
                            -$creditCost,
                            '运行任务消耗(ID=' . $task['id'] . ')',
                            null,
                            [
                                'checkNegative' => false,
                            ]
                        );
                    }
                    ModelUtil::transactionCommit();
                }
            } elseif ($ret['data']['status'] == 'FAIL') {
                $errorMsg = 'callQuery.fail';
                if (!empty($ret['data']['result']['msg'])) {
                    $errorMsg = $ret['data']['result']['msg'];
                }
                $this->markFail($errorMsg, true);
            } else {
                LogUtil::info('AigcTaskProcessJob.queryLater - ' . $this->taskId, $ret);
                self::create($this->taskId, 5);
            }
        }
    }
}
