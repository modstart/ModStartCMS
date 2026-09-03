<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Module\PayCenter\Util\PayOrderUtil;

/*
 * 数据回填迁移：将该业务（Member）下历史上已支付但 memberUserId 为空的
 * pay_order 从业务订单表反查并回填，使 pay_order 成为统一的付费事实源。
 * 逻辑基于 PayCenter::PayOrderUtil::backfillMemberUserIdByBiz()，
 * 业务订单表不存在或字段缺失时自动跳过，幂等安全。
 */
class MemberPayOrderMemberUserIdBackfill extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('pay_order') || !Schema::hasColumn('pay_order', 'memberUserId')) {
            return;
        }
        PayOrderUtil::backfillMemberUserIdByBiz(\Module\Member\Core\MemberMoneyChargePayCenterBiz::NAME);
        PayOrderUtil::backfillMemberUserIdByBiz(\Module\Member\Core\MemberVipPayCenterBiz::NAME);
    }

    public function down()
    {
    }
}
