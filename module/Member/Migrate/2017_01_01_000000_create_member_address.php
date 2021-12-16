<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMemberAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_address', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('userId')->nullable()->comment('用户ID');

            $table->string('name', 20)->nullable()->comment('姓名');
            $table->string('phone', 20)->nullable()->comment('手机号');
            $table->string('area', 100)->nullable()->comment('省市地区');
            $table->string('detail', 200)->nullable()->comment('详细地址');
            $table->string('post', 20)->nullable()->comment('邮政编码');

            $table->tinyInteger('isDefault')->nullable()->comment('默认');

            $table->index(['userId']);

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
