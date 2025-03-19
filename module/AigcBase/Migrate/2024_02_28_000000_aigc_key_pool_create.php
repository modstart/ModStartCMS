<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AigcKeyPoolCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('aigc_key_pool', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->timestamps();

            /** @see \Module\AigcBase\Provider\AigcChatProvider */
            $table->string('type', 20)->nullable()->comment('');
            /** @see \Module\AigcBase\Type\AigcKeyPoolStatus */
            $table->tinyInteger('status')->nullable()->comment('');

            $table->integer('priority')->nullable()->comment('');

            $table->text('param')->nullable()->comment('');
            $table->string('remark', 100)->nullable()->comment('');

            $table->dateTime('lastCallTime')->nullable()->comment('');
            $table->bigInteger('callCount')->nullable()->comment('');
            $table->bigInteger('successCount')->nullable()->comment('');
            $table->bigInteger('failCount')->nullable()->comment('');

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
