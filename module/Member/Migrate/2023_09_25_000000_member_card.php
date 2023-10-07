<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MemberCard extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_card', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->timestamps();

            $table->bigInteger('memberUserId')->nullable()->comment('用户ID');
            $table->string('biz', 20)->nullable()->comment('业务');
            $table->dateTime('expire')->nullable()->comment('到期时间');

            $table->bigInteger('quotaUsed')->nullable()->comment('使用额度');
            $table->bigInteger('quotaTotal')->nullable()->comment('总额度');

            $table->index(['memberUserId', 'biz']);

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
