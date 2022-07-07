<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMemberDeleteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_user', function (Blueprint $table) {

            $table->bigInteger('deleteAtTime')->nullable()->comment('已删除');
            $table->tinyInteger('isDeleted')->nullable()->comment('已删除');
            $table->index(['deleteAtTime']);

        });

        Schema::create('member_deleted', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('username', 50)->nullable()->comment('用户名');
            $table->string('phone', 20)->nullable()->comment('手机');
            $table->string('email', 200)->nullable()->comment('邮箱');
            $table->text('content')->comment('其他数据');
        });

        \ModStart\Core\Dao\ModelUtil::updateAll('member_user', [
            'isDeleted' => false,
        ]);

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
