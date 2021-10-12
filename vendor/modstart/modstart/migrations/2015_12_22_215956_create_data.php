<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->string('category', 10)->nullable()->comment('大类');
            $table->string('path', 40)->nullable()->comment('路径');
            $table->string('filename', 200)->nullable()->comment('原始文件名');
            $table->unsignedInteger('size');
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
