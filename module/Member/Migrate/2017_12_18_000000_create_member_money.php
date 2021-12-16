<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberMoney extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!\ModStart\Core\Dao\ModelManageUtil::hasTable('member_money')) {
            Schema::create('member_money', function (Blueprint $table) {

                $table->increments('id');
                $table->timestamps();

                $table->integer('memberUserId')->nullable()->comment('用户ID');
                $table->decimal('total', 20, 2)->nullable()->comment('金额');

                $table->unique(['memberUserId']);

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
