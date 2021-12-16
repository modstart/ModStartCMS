<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use ModStart\Core\Dao\ModelManageUtil;

class CreateAtomic extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!ModelManageUtil::hasTable('atomic')) {
            Schema::create('atomic', function (Blueprint $table) {

                $table->increments('id');
                $table->timestamps();

                $table->string('name', 100)->nullable()->comment('');
                $table->integer('value')->nullable()->comment('');
                $table->integer('expire')->nullable()->comment('');

                $table->unique('name');
                $table->index('expire');
            });
        }
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
