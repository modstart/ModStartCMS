<?php


namespace Module\Member\Util;


use Illuminate\Database\Eloquent\Model;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\FileUtil;
use ModStart\Module\ModuleManager;
use Module\Member\Model\MemberDataStatistic;
use Module\Member\Model\MemberUpload;

class MemberDataStatisticUtil
{
    public static function checkQuota($memberUserId)
    {
        if (!$memberUserId) {
            BizException::throws('请先登录');
        }
        if (
            !ModuleManager::getModuleConfig('Member', 'dataStatisticEnable', false)
            ||
            !modstart_config('Member_DataStatisticEnable', false)
        ) {
            return;
        }
        $record = MemberDataStatistic::getCreateMemberUser($memberUserId);
        BizException::throwsIfEmpty('获取用户数据统计失败', $record);
        $sizeLimitBytes = $record['sizeLimit'] * 1024 * 1024;
        if ($record['sizeUsed'] >= $sizeLimitBytes) {
            BizException::throws('您的上传空间已满，最多可以上传' . FileUtil::formatByte($sizeLimitBytes));
        }
    }

    public static function getCreateMemberUser($id)
    {
        $first = MemberDataStatistic::where('id', $id)->first();
        if (empty($first)) {
            $m = new MemberDataStatistic();
            $m->id = $id;
            $m->sizeLimit = modstart_config('Member_DataStatisticDefaultLimit', 1024);
            $m->save();
            self::updateMemberUserUsedSize($id);
            return self::getCreateMemberUser($id);
        }
        return $first->toArray();
    }

    public static function updateMemberUser($id, $data)
    {
        $m = MemberDataStatistic::where('id', $id)->first();
        $updateSize = false;
        if (empty($m)) {
            $m = new MemberDataStatistic();
            $m->id = $id;
            $updateSize = true;
        }
        foreach ($data as $k => $v) {
            $m->$k = $v;
        }
        $m->save();
        if ($updateSize) {
            self::updateMemberUserUsedSize($id);
        }
    }

    public static function calcMemberUserUsedSize($id)
    {
        $total = MemberUpload::where(['userId' => $id])
            ->leftJoin('data', 'data.id', '=', 'member_upload.dataId')
            ->sum('data.size');
        return intval($total);
    }

    public static function updateMemberUserUsedSize($id)
    {
        $update = [
            'sizeUsed' => self::calcMemberUserUsedSize($id),
        ];
        MemberDataStatistic::where('id', $id)->update($update);
    }
}
