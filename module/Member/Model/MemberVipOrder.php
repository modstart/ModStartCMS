<?php


namespace Module\Member\Model;


use Illuminate\Database\Eloquent\Model;
use Module\Member\Core\MemberVipPayCenterBiz;
use Module\PayCenter\Model\PayOrder;

class MemberVipOrder extends Model
{
    protected $table = 'member_vip_order';

    public function payOrder()
    {
        return $this->hasOne(PayOrder::class, 'bizId', 'id')
            ->where('biz', MemberVipPayCenterBiz::NAME);
    }
}
