<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberMoneyLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!\ModStart\Core\Dao\ModelManageUtil::hasTable('member_money_log')) {
            Schema::create('member_money_log', function (Blueprint $table) {

                $table->increments('id');
                $table->timestamps();

                $table->integer('memberUserId')->nullable()->comment('用户ID');
                $table->decimal('change', 20, 2)->nullable()->comment('金额');
                $table->string('remark', 100)->nullable()->comment('备注');

                $table->index(['memberUserId']);

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
