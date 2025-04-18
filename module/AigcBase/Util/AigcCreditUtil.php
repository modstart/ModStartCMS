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
            $count = call_user_func_array($countCalculator, []);
        } else {
            $count = $countCalculator;
        }
        $amount = ceil($count / $unit);
        return $amount * $cost;
    }

    /**
     * 计算长度，1个汉字2个字符，1个英文字母1个字符
     * @param $text string
     * @return int
     */
    public static function countChar($text)
    {
        // 1个汉字2个字符
        $text = preg_replace('/[\x{4e00}-\x{9fa5}]/u', 'xx', $text);
        // 1个英文字母1个字符
        // ignore
        $text = preg_replace('/[a-zA-Z]/u', 'x', $text);
        return mb_strlen($text);
    }

    public static function calcContentCharLength($configKeyPrefix, $content)
    {
        return self::calc($configKeyPrefix, self::countChar($content));
    }

    public static function change($memberUserId, $change, $remark)
    {
        ModelUtil::transactionBegin();
        MemberCreditUtil::change($memberUserId, $change, $remark, null, [
            'checkNegative' => false
        ]);
        ModelUtil::transactionCommit();
    }

    public static function changeByContentCharLength($memberUserId, $configKeyPrefix, $content, $remark)
    {
        $amount = self::calcContentCharLength($configKeyPrefix, $content);
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
