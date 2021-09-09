<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyArticleAddSort extends Migration
{
    
    public function up()
    {
        Schema::table('article', function (Blueprint $table) {
            $table->integer('sort')->nullable()->comment('');
        });
    }

    
    public function down()
    {

    }
}
