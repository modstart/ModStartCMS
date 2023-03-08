<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use ModStart\Core\Dao\ModelUtil;
use Module\Member\Type\MemberMessageStatus;
use Module\Member\Util\MemberUtil;

class ModifyMemberUserMessageCount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_user', function (Blueprint $table) {
            $table->integer('messageCount')->nullable()->comment('未读消息数量');
        });

        $records = ModelUtil::model('member_message')
            ->where(['status' => MemberMessageStatus::UNREAD])
            ->groupBy('userId')
            ->get(['userId', \DB::raw('count(*) as count')])
            ->toArray();
        foreach ($records as $record) {
            MemberUtil::update($record['userId'], [
                'messageCount' => $record['count']
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
