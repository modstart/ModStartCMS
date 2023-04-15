<?php


namespace Module\Member\Provider;


use ModStart\Core\Dao\ModelUtil;
use Module\Member\Util\MemberUtil;
use Module\Vendor\Provider\Schedule\AbstractScheduleBiz;

class MemberDeleteScheduleProvider extends AbstractScheduleBiz
{
    public function cron()
    {
        return $this->cronEveryMinute();
    }

    public function title()
    {
        return '删除申请注销账号的用户';
    }

    public function run()
    {
        $records = ModelUtil::model('member_user')
            ->where('deleteAtTime', '>', 0)
            ->where('deleteAtTime', '<', time())
            ->where(['isDeleted' => false])
            ->get()->toArray();
        foreach ($records as $order) {
            MemberUtil::delete($order['id']);
        }
    }

}
