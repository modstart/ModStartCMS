<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AigcWorkCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('aigc_work', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->timestamps();

            $table->string('biz', 20)->nullable()->comment('');

            /** @see \Module\Vendor\Type\JobStatus */
            $table->tinyInteger('status')->nullable()->comment('');
            $table->string('statusRemark', 100)->nullable()->comment('');

            $table->dateTime('startTime')->nullable()->comment('');
            $table->integer('cost')->nullable()->comment('');

            $table->string('param', 400)->nullable()->comment('');
            $table->string('result', 400)->nullable()->comment('');

            $table->index(['biz']);
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
