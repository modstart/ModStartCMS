<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMemberAddressUserIdFix extends Migration
{
    
    public function up()
    {
        Schema::table('member_address', function (Blueprint $table) {
            $table->renameColumn('userId', 'memberUserId');
        });

    }

    
    public function down()
    {

    }
}
