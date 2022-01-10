<?php

use Illuminate\Database\Migrations\Migration;

class MemberMemberVipInit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\ModStart\Core\Dao\ModelUtil::count('member_vip_set') <= 0) {
            \ModStart\Core\Dao\ModelUtil::insertAll('member_vip_set', [
                [
                    'id' => 1,
                    'flag' => 'default',
                    'title' => '普通会员',
                    'price' => '0.00',
                    'vipDays' => 0,
                    'isDefault' => true,
                    'content' => '<p>普通会员说明</p>',
                ],
                [
                    'id' => 2,
                    'flag' => 'vip1',
                    'title' => 'VIP黄金会员',
                    'price' => '9.99',
                    'vipDays' => 30,
                    'isDefault' => false,
                    'content' => '<p>VIP黄金会员说明</p>',
                ],
                [
                    'id' => 3,
                    'flag' => 'vip2',
                    'title' => 'VIP钻石会员',
                    'price' => '99.99',
                    'vipDays' => 365,
                    'isDefault' => false,
                    'content' => '<p>VIP钻石会员说明</p>',
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
