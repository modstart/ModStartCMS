<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyMemberOauthAddInfo extends Migration
{
    
    public function up()
    {
        Schema::table('member_oauth', function (Blueprint $table) {
            $table->string('infoUsername', 100)->nullable();
            $table->string('infoAvatar', 200)->nullable();
        });
    }

    
    public function down()
    {
    }
}
