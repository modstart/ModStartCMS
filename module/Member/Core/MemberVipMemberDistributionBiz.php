<?php


namespace Module\Member\Core;


use Module\MemberDistribution\Biz\AbstractMemberDistributionBiz;

class MemberVipMemberDistributionBiz extends AbstractMemberDistributionBiz
{
    const NAME = 'MemberVip';

    public function name()
    {
        return self::NAME;
    }

    public function title()
    {
        return '用户VIP充值';
    }

}
