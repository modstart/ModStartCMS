<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MemberCreditFreeze extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_credit_freeze', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->timestamps();

            $table->bigInteger('memberUserId')->nullable()->comment('用户ID');
            $table->bigInteger('value')->nullable()->comment('数量');
            /** @see \Module\Member\Type\MemberCreditFreezeStatus */
            $table->tinyInteger('status')->nullable()->comment('数量');
            $table->string('remark', 100)->nullable()->comment('备注');
            $table->string('meta', 100)->nullable()->comment('额外信息');

            $table->dateTime('freezeAt')->nullable()->comment('冻结时间');
            $table->dateTime('commitAt')->nullable()->comment('提交时间');
            $table->dateTime('cancelAt')->nullable()->comment('取消时间');

            $table->index(['memberUserId']);

        });

        Schema::table('member_credit', function (Blueprint $table) {
            $table->bigInteger('freezeTotal')->nullable()->comment('冻结额度');
        });
        Schema::table('member_credit_log', function (Blueprint $table) {
            $table->string('meta', 100)->nullable()->comment('额外信息');
        });
        Schema::table('member_money_log', function (Blueprint $table) {
            $table->string('meta', 100)->nullable()->comment('额外信息');
        });
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
