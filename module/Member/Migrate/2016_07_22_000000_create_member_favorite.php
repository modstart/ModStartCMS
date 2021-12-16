<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberFavorite extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_favorite', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('userId')->comment('用户ID')->nullable();
            $table->string('category', 20)->comment('类别')->nullable();
            $table->integer('categoryId')->comment('所属类别ID')->nullable();

            $table->index(['userId', 'category']);

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
