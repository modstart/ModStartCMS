<?php

namespace Module\Vendor\QuickRun\JobManager;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Job\BaseJob;
use ModStart\Core\Util\LogUtil;
use ModStart\Core\Util\SerializeUtil;
use ModStart\Core\Util\StrUtil;
use Module\Vendor\Type\JobStatus;

class JobManager
{
    protected $param = [];
    protected $model;
    protected $id;
    protected $modelName;

    public static function dispatch(BaseJob $job, $delay = 0, $queue = 'default')
    {
        $job->onQueue($queue);
        if ($delay > 0) {
            $job->delay($delay);
        }
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }

    public static function create($model, $id, $param)
    {
        $ins = new static();
        $ins->model = $model;
        $ins->id = $id;
        $ins->modelName = class_basename($model);
        $ins->param = array_merge([
            'statusField' => 'status',
            'statusRemarkField' => 'statusRemark',
            'statusRemarkFieldLength' => 400,
            'startTimeField' => 'startTime',
            'endTimeField' => 'endTime',
        ], $param);
        return $ins;
    }

    public function getQueued($statusCheck = null)
    {
        if (is_null($statusCheck)) {
            $statusCheck = [
                JobStatus::QUEUE
            ];
        }
        ModelUtil::transactionBegin();
        $record = ModelUtil::getWithLock($this->model, $this->id);
        LogUtil::info('JobManager.getQueuedJob.' . $this->modelName, [
            'id' => $this->id,
        ]);
        if (empty($record)) {
            ModelUtil::transactionCommit();
            LogUtil::info('JobManager.getQueuedJob.' . $this->modelName . '.notFound', [
                'id' => $this->id,
            ]);
            return null;
        }
        if (!in_array($record[$this->param['statusField']], $statusCheck)) {
            ModelUtil::transactionCommit();
            LogUtil::info('JobManager.getQueuedJob.' . $this->modelName . '.notQueued', [
                'id' => $this->id,
                'status' => $record[$this->param['statusField']],
            ]);
            return null;
        }
        $update = [];
        if ($record[$this->param['statusField']] == JobStatus::QUEUE) {
            if ($this->param['startTimeField']) {
                $update[$this->param['startTimeField']] = date('Y-m-d H:i:s');
            }
            if ($this->param['endTimeField']) {
                $update[$this->param['endTimeField']] = null;
            }
        }
        $update[$this->param['statusField']] = JobStatus::PROCESS;
        ModelUtil::update($this->model, $this->id, $update);
        ModelUtil::transactionCommit();
        unset($record[$this->param['statusField']]);
        unset($record[$this->param['statusRemarkField']]);
        return $record;
    }

    public function getQueuedOrProcessWait()
    {
        return $this->getQueued([
            JobStatus::QUEUE,
            JobStatus::PROCESS_WAIT,
        ]);
    }

    public function markSuccess($update = [], $status = JobStatus::SUCCESS)
    {
        foreach ($update as $k => $v) {
            if (is_array($v)) {
                $update[$k] = SerializeUtil::jsonEncode($v);
            }
        }
        if ($status == JobStatus::SUCCESS) {
            if ($this->param['endTimeField']) {
                $update[$this->param['endTimeField']] = date('Y-m-d H:i:s');
            }
        }
        $update[$this->param['statusField']] = $status;
        $update[$this->param['statusRemarkField']] = '';
        LogUtil::info('JobManager.markSuccess.' . $this->modelName, [
            'id' => $this->id,
            'update' => $update,
        ]);
        ModelUtil::update($this->model, $this->id, $update);
    }

    public function markProcessWait($update = [])
    {
        $this->markSuccess($update, JobStatus::PROCESS_WAIT);
    }

    public function markFail($remark, $update = [])
    {
        if ($this->param['endTimeField']) {
            $update[$this->param['endTimeField']] = date('Y-m-d H:i:s');
        }
        $update[$this->param['statusField']] = JobStatus::FAIL;
        $update[$this->param['statusRemarkField']] = StrUtil::mbLimit($remark, $this->param['statusRemarkFieldLength']);
        LogUtil::info('JobManager.markFail.' . $this->modelName, [
            'id' => $this->id,
            'remark' => $remark,
            'update' => $update,
        ]);
        ModelUtil::update($this->model, $this->id, $update);
    }
}
