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
                    'title' => '黄金月卡',
                    'price' => '19.99',
                    'vipDays' => 30,
                    'isDefault' => false,
                    'content' => '<p>黄金月卡会员</p>
<p>30天有效期</p>
<p>享受黄金会员福利</p>',
                ],
                [
                    'id' => 3,
                    'flag' => 'vip2',
                    'title' => '钻石季卡',
                    'price' => '49.99',
                    'vipDays' => 90,
                    'isDefault' => false,
                    'content' => '<p>钻石季卡会员</p>
<p>90天有效期</p>
<p>享受钻石会员福利</p>',
                ],
                [
                    'id' => 4,
                    'flag' => 'vip3',
                    'title' => '至尊年卡',
                    'price' => '189.99',
                    'vipDays' => 365,
                    'isDefault' => false,
                    'content' => '<p>至尊年卡会员</p>
<p>365天有效期</p>
<p>享受至尊会员福利</p>',
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
