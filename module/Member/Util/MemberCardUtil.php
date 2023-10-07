<?php


namespace Module\Member\Util;


use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\Response;
use Module\Member\Model\MemberCard;

class MemberCardUtil
{
    public static function create($memberUserId, $biz, $quota, $expire = null)
    {
        $data = [];
        $data['memberUserId'] = $memberUserId;
        $data['biz'] = $biz;
        $data['expire'] = $expire;
        $data['quotaUsed'] = 0;
        $data['quotaTotal'] = $quota;

        $data = ModelUtil::insert('member_card', $data);
        return $data;
    }

    /**
     * 消耗会员卡
     * @param $memberUserId integer 会员用户ID
     * @param $biz string 模块
     * @param $quota int 消耗额度
     * @return array 是否消耗成功
     */
    public static function consume($memberUserId, $biz, $quota = 1)
    {
        ModelUtil::transactionBegin();
        $card = MemberCard::where(['memberUserId' => $memberUserId, 'biz' => $biz])
            ->where('expire', '>', date('Y-m-d H:i:s'))
            ->whereRaw('quotaUsed+' . intval($quota) . ' <= quotaTotal')
            ->orderBy('expire', 'asc')
            ->first();
        if (empty($card)) {
            ModelUtil::transactionCommit();
            return Response::generateError('额度不足');
        }
        $card->quotaUsed += $quota;
        $card->save();
        ModelUtil::transactionCommit();
        return Response::generateSuccess();
    }
}
