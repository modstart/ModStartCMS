<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_group', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('title', 50)->nullable()->comment('名称');
            $table->string('description', 200)->nullable()->comment('描述');
            $table->tinyInteger('isDefault')->nullable()->comment('默认');
        });

        Schema::table('member_user', function (Blueprint $table) {
            $table->integer('groupId')->nullable()->comment('');
        });

        \ModStart\Core\Dao\ModelUtil::insertAll('member_group', [
            [
                'title' => '普通用户',
                'description' => '',
                'isDefault' => true,
            ],
            [
                'title' => '高级用户',
                'description' => '',
                'isDefault' => false,
            ],
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
