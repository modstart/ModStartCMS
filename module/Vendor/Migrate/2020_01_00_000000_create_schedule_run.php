<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use ModStart\Core\Dao\ModelManageUtil;

class CreateScheduleRun extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_run', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->timestamps();

            $table->string('name', 200)->nullable()->comment('');
            $table->dateTime('startTime')->nullable()->comment('');
            $table->dateTime('endTime')->nullable()->comment('');
            /** @see \Module\Vendor\Provider\Schedule\RunStatus */
            $table->tinyInteger('status')->nullable()->comment('');
            $table->string('result', 200)->nullable()->comment('');

            $table->index('created_at');
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
