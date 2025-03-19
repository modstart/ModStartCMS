<?php

namespace Module\AigcBase\Util;

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\Response;
use Module\Member\Auth\MemberUser;
use Module\Member\Util\MemberCreditUtil;

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

    public static function calcText($configKeyPrefix, $content)
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

    public static function changeByContent($configKeyPrefix, $userId, $content, $remark)
    {
        $amount = self::calcText($configKeyPrefix, $content);
        self::change($userId, -$amount, $remark);
    }

}
