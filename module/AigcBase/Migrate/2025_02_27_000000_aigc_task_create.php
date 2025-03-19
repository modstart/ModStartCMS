<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AigcTaskCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('aigc_task', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->timestamps();

            $table->bigInteger('memberUserId')->nullable()->comment('');
            $table->string('biz', 20)->nullable()->comment('');

            /** @see \Module\Vendor\Type\JobStatus */
            $table->tinyInteger('status')->nullable()->comment('');
            $table->string('statusRemark', 100)->nullable()->comment('');

            $table->dateTime('startTime')->nullable()->comment('');
            $table->integer('cost')->nullable()->comment('');

            $table->text('modelConfig')->nullable()->comment('');
            $table->text('result')->nullable()->comment('');

            $table->integer('creditCost')->nullable()->comment('');
            //$table->string('resultProvider', 20)->nullable()->comment('');

            $table->index(['memberUserId', 'biz']);
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
