<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyArticleAddAlias extends Migration
{
    
    public function up()
    {
        Schema::table('article', function (Blueprint $table) {
            $table->string('alias', 50)->nullable()->comment('');
            $table->index('alias');
        });
    }

    
    public function down()
    {

    }
}
