<?php


namespace Module\Member\Util;


use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\FileUtil;
use ModStart\Module\ModuleManager;
use Module\Member\Model\MemberDataStatistic;

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
}
