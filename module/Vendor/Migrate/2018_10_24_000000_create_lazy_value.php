<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLazyValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lazy_value', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->string('key', 50)->nullable()->comment('');
            $table->string('param', 100)->nullable()->comment('');
            $table->integer('expire')->nullable()->comment('');
            $table->integer('lifeExpire')->nullable()->comment('');
            $table->integer('cacheSeconds')->nullable()->comment('');
            $table->text('value')->nullable()->comment('');

            $table->unique(['key', 'param']);
            $table->index(['expire']);
            $table->index(['lifeExpire']);

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