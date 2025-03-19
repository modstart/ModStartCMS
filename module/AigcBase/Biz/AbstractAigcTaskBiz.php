<?php

namespace Module\AigcBase\Biz;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\FileUtil;
use ModStart\Core\Util\PathUtil;
use ModStart\Core\Util\SerializeUtil;
use ModStart\Data\DataManager;
use Module\AigcBase\Job\AigcTaskProcessJob;
use Module\AigcBase\Model\AigcTask;
use Module\Member\Util\MemberCreditUtil;
use Module\Vendor\Type\JobStatus;

abstract class AbstractAigcTaskBiz
{
    abstract public function name();

    abstract public function title();

    /**
     * @param $modelConfig
     * @param $param
     * @return array
     * @example
     * [
     *  'code' => 0,
     *  'msg' => '',
     *  'data'=>[
     *      'result'=>[ 'foo'=>'bar',]
     *   ]
     * ]
     */
    abstract public function callQueue($modelConfig, $param = []);

    /**
     * @param $result
     * @param $param
     * @return array
     * @example
     *  [
     *   'code' => 0,
     *   'msg' => '',
     *   'data'=>[
     *       'status'=>'SUCCESS|FAIL|RUNNING',
     *       'result'=>[ 'foo'=>'bar',]
     *    ]
     *  ]
     * /
     */
    abstract public function callQuery($result, $param = []);

    /**
     * @param $result
     * @param $param
     * @return integer
     */
    abstract public function creditCost($result, $param = []);

    public static function submit($memberUserId, $modelConfig)
    {
        if (MemberCreditUtil::getTotal($memberUserId) <= 0) {
            return Response::generateError(modstart_module_config('Member', 'creditName', '积分') . '不足');
        }
        $data = [];
        $data['memberUserId'] = $memberUserId;
        $data['biz'] = static::NAME;
        $data['status'] = JobStatus::QUEUE;
        $data['modelConfig'] = SerializeUtil::jsonEncode($modelConfig);
        $data['creditCost'] = 0;
        $data = ModelUtil::insert(AigcTask::class, $data);
        AigcTaskProcessJob::create($data['id']);
        return Response::generateSuccessData($data);
    }

    public static function get($memberUserId, $id)
    {
        $task = ModelUtil::get(AigcTask::class, [
            'memberUserId' => $memberUserId,
            'id' => $id,
            'biz' => static::NAME
        ]);
        if (empty($task)) {
            return Response::generateError('任务不存在');
        }
        ModelUtil::decodeRecordJson($task, ['modelConfig', 'result']);
        if (empty($task['result'])) {
            $task['result'] = new \stdClass();
        }
        if (!empty($task['result']['url'])) {
            $ext = PathUtil::getExtention($task['result']['url']);
            $name = $task['id'];
            if (isset($task['modelConfig']['text'])) {
                $name = FileUtil::textToFilename($task['modelConfig']['text'], 10);
            }
            $task['result']['_urlName'] = $name . '.' . $ext;
        }
        return Response::generateSuccessData($task);
    }

    public static function all($memberUserId, $option = [])
    {
        $query = AigcTask::where([
            'memberUserId' => $memberUserId,
            'biz' => static::NAME
        ])->orderBy('id', 'desc');
        if (!empty($option['status'])) {
            $statusMap = [
                'success' => [JobStatus::SUCCESS],
                'processing' => [JobStatus::QUEUE, JobStatus::PROCESS],
                'failed' => [JobStatus::FAIL],
            ];
            if (isset($statusMap[$option['status']])) {
                $query = $query->whereIn('status', $statusMap[$option['status']]);
            }
        }
        $records = $query->get(['id', 'modelConfig', 'status', 'created_at', 'result', 'startTime', 'cost', 'creditCost'])->toArray();
        ModelUtil::decodeRecordsJson($records, ['modelConfig', 'result']);
        foreach ($records as $i => $v) {
            if (empty($v['result'])) {
                $records[$i]['result'] = new \stdClass();
            }
            if (!empty($v['result']['url'])) {
                $ext = PathUtil::getExtention($v['result']['url']);
                $name = $v['id'];
                if (isset($v['modelConfig']['text'])) {
                    $name = FileUtil::textToFilename($v['modelConfig']['text'], 10);
                }
                $records[$i]['result']['_urlName'] = $name . '.' . $ext;
            }
        }
        return $records;
    }

    public static function paginate($memberUserId, $page, $pageSize, $option = [])
    {
        $result = [
            'page' => $page,
            'pageSize' => $pageSize,
            'total' => 0,
            'records' => []
        ];
        $query = AigcTask::where([
            'memberUserId' => $memberUserId,
            'biz' => static::NAME
        ])->orderBy('id', 'desc');
        if (!empty($option['status'])) {
            $statusMap = [
                'success' => [JobStatus::SUCCESS],
                'processing' => [JobStatus::QUEUE, JobStatus::PROCESS],
                'failed' => [JobStatus::FAIL],
            ];
            if (isset($statusMap[$option['status']])) {
                $query = $query->whereIn('status', $statusMap[$option['status']]);
            }
        }
        $paginateResult = $query->paginate($pageSize,
            ['id', 'modelConfig', 'status', 'created_at', 'result', 'startTime', 'cost', 'creditCost'],
            'page', $page)->toArray();
        $result['total'] = $paginateResult['total'];
        $records = $paginateResult['data'];
        ModelUtil::decodeRecordsJson($records, ['modelConfig', 'result']);
        foreach ($records as $i => $v) {
            if (empty($v['result'])) {
                $records[$i]['result'] = new \stdClass();
            }
            if (!empty($v['result']['url'])) {
                $ext = PathUtil::getExtention($v['result']['url']);
                $name = $v['id'];
                if (isset($v['modelConfig']['text'])) {
                    $name = FileUtil::textToFilename($v['modelConfig']['text'], 10);
                }
                $records[$i]['result']['_urlName'] = $name . '.' . $ext;
            }
        }
        $result['records'] = $records;
        return Response::generateSuccessData($result);
    }

    public static function delete($memberUserId, $id)
    {
        $task = ModelUtil::get(AigcTask::class, [
            'memberUserId' => $memberUserId,
            'id' => $id
        ]);
        if (empty($task)) {
            return Response::generateError('任务不存在');
        }
        if ($task['status'] == JobStatus::PROCESS) {
            return Response::generateError('任务状态不允许删除');
        }
        ModelUtil::decodeRecordJson($task, ['modelConfig', 'result']);
        if (!empty($task['result']['url'])) {
            DataManager::deleteByPath($task['result']['url']);
        }
        ModelUtil::delete(AigcTask::class, $task['id']);
        return Response::generateSuccess();
    }

}
