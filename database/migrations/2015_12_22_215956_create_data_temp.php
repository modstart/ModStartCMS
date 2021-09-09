<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataTemp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_temp', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->string('category', 10)->comment('大类')->nullable();
            $table->string('path', 40)->comment('路径')->nullable();
            $table->string('filename', 200)->comment('原始文件名')->nullable();
            $table->unsignedInteger('size')->comment('文件大小')->nullable();

            $table->index(['category', 'path']);
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
