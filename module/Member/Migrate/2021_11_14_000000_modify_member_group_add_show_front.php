<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ModifyMemberGroupAddShowFront extends Migration
{
    
    public function up()
    {
        Schema::table('member_group', function (Blueprint $table) {
            $table->tinyInteger('showFront')->nullable()->comment('前台显示');
        });
    }

    
    public function down()
    {

    }
}
