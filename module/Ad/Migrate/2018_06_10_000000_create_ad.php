<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAd extends Migration
{
    
    public function up()
    {
        Schema::create('ad', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->string('position', 50)->nullable()->comment('位置');

            $table->integer('sort')->nullable()->comment('顺序');
            $table->string('image', 100)->nullable()->comment('图片');
            $table->string('link', 200)->nullable()->comment('链接');

        });
    }

    
    public function down()
    {
    }
}
