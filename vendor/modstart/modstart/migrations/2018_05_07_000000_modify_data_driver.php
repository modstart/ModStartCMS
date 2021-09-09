<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyDataDriver extends Migration
{
    
    public function up()
    {
        Schema::table('data', function (Blueprint $table) {

            $table->string('driver', 16)->nullable()->comment('大类');
            $table->string('domain', 100)->nullable()->comment('域名');

        });
    }

    
    public function down()
    {
    }
}
