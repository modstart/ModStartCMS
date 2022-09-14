<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MemberMemberVipCredit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_vip_set', function (Blueprint $table) {

            $table->tinyInteger('creditPresentEnable')->nullable()->comment('');
            $table->integer('creditPresentValue')->nullable()->comment('');

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
