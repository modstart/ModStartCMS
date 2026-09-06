<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MemberUserAddBizGroup extends Migration
{
    public function up()
    {
        Schema::table('member_user', function (Blueprint $table) {
            $table->string('bizGroup', 10)->nullable()->comment('');
        });
    }

    public function down()
    {
    }
}