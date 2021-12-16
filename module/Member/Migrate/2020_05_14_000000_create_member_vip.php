<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberVip extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_user', function (Blueprint $table) {

            $table->integer('vipId')->nullable()->comment('');
            $table->date('vipExpire')->nullable()->comment('');

        });

        Schema::create('member_vip_set', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->string('title', 50)->nullable()->comment('名称');
            $table->string('flag', 50)->nullable()->comment('标识');
            $table->integer('pid')->nullable()->comment('排序');
            $table->integer('sort')->nullable()->comment('排序');
            $table->tinyInteger('isDefault')->nullable()->comment('默认');
            $table->string('icon', 100)->nullable()->comment('图标');

            $table->decimal('price', 20, 2)->nullable()->comment('');
            $table->integer('vipDays')->nullable()->comment('');
            $table->text('content')->nullable()->comment('说明');

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
