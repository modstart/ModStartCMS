<?php

namespace Module\AigcBase\Util;

use ModStart\Core\Dao\ModelUtil;
use Module\Member\Util\MemberCreditUtil;
use Module\MemberQuota\Util\MemberQuotaUtil;

class AigcCreditUtil
{
    public static function calc($configKeyPrefix, $countCalculator)
    {
        $unit = modstart_config($configKeyPrefix . 'Unit', 1);
        $cost = modstart_config($configKeyPrefix . 'Cost', 1);
        if (is_callable($countCalculator)) {
            $count = call_user_func_array($countCalculator, [
                $unit
            ]);
        } else {
            $count = $countCalculator;
        }
        $amount = ceil($count / $unit);
        return $amount * $cost;
    }

    public static function calcContentLength($configKeyPrefix, $content)
    {
        return self::calc($configKeyPrefix, mb_strlen($content));
    }

    public static function change($memberUserId, $change, $remark)
    {
        ModelUtil::transactionBegin();
        MemberCreditUtil::change($memberUserId, $change, $remark, null, [
            'checkNegative' => false
        ]);
        ModelUtil::transactionCommit();
    }

    public static function changeByContentLength($memberUserId, $configKeyPrefix, $content, $remark)
    {
        $amount = self::calcContentLength($configKeyPrefix, $content);
        self::change($memberUserId, -$amount, $remark);
    }

    public static function checkQuotaCreditOrFail($memberUserId, $quotaBiz, $quotaValue = 1, $creditValue = 1)
    {
        $pass = false;
        if (modstart_module_enabled('MemberQuota')) {
            $pass = MemberQuotaUtil::check($memberUserId, $quotaBiz, $quotaValue);
        }
        if (!$pass) {
            MemberCreditUtil::checkOrFail($memberUserId, $creditValue);
        }
    }

}
