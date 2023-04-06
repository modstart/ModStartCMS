<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMemberVipRight extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('member_vip_right', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->timestamps();

            $table->string('vipIds', 200)->nullable()->comment('VIPID');
            $table->string('title', 200)->nullable()->comment('标题');
            $table->string('desc', 200)->nullable()->comment('描述');
            $table->string('image', 200)->nullable()->comment('图标');
            $table->integer('sort')->nullable()->comment('排序');

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
