<?php

namespace Module\AigcBase\Util;

use Module\MemberQuota\Util\MemberQuotaUtil;

class AigcQuotaUtil
{
    public static function consume($memberUserId, $quotaBiz, $quotaValue, $remark)
    {
        if (!modstart_module_enabled('MemberQuota')) {
            return false;
        }
        if (!MemberQuotaUtil::check($memberUserId, $quotaBiz, $quotaValue)) {
            return false;
        }
        MemberQuotaUtil::consume($memberUserId, $quotaBiz, $quotaValue, $remark);
        return true;
    }
}
