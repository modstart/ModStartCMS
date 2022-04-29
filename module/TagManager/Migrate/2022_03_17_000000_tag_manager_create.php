<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class TagManagerCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('tag_manager', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->string('biz', 10)->nullable()->comment('');
            $table->string('tag', 50)->nullable()->comment('');
            $table->integer('cnt')->nullable()->comment('');
            $table->tinyInteger('isShow')->nullable()->comment('');

            $table->unique(['biz', 'tag']);
            $table->index(['biz', 'tag', 'cnt']);
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
