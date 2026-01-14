<?php


namespace Module\Member\Provider;


use Module\Member\Model\MemberUser;
use Module\Member\Util\MemberUtil;
use Module\Vendor\Provider\Schedule\AbstractScheduleBiz;

class MemberDeleteScheduleProvider extends AbstractScheduleBiz
{
    public function cron()
    {
        return $this->cronEveryHour();
    }

    public function title()
    {
        return '删除申请注销账号的用户';
    }

    public function run()
    {
        $records = MemberUser::where('deleteAtTime', '>', 0)
            ->where('deleteAtTime', '<', time())
            ->where(['isDeleted' => false])
            ->get()->toArray();
        foreach ($records as $order) {
            MemberUtil::delete($order['id']);
        }
    }

}
