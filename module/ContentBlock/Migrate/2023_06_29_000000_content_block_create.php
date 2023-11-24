<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ContentBlockCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('content_block', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->timestamps();

            $table->string('type', 20)->nullable()->comment('');

            $table->string('name', 50)->nullable()->comment('');
            $table->string('title', 200)->nullable()->comment('');
            $table->string('image', 200)->nullable()->comment('');
            $table->string('link', 200)->nullable()->comment('');
            $table->text('content')->nullable()->comment('');

            $table->integer('sort')->nullable()->comment('');
            $table->dateTime('startTime')->nullable()->comment('');
            $table->dateTime('endTime')->nullable()->comment('');
            $table->tinyInteger('enable')->nullable()->comment('');

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
