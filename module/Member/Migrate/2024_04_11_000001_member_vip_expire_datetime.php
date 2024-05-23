<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MemberVipExpireDatetime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_user', function (Blueprint $table) {
            $table->dateTime('vipExpire')->nullable()->comment('VIP过期时间')->change();
        });

        Schema::table('member_vip_order', function (Blueprint $table) {
            $table->dateTime('expire')->nullable()->comment('VIP过期时间')->change();
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
