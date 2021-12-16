<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ModifyMemberUserAddNickname extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!\ModStart\Core\Dao\ModelManageUtil::hasTableColumn('member_user', 'nickname')) {
            Schema::table('member_user', function (Blueprint $table) {

                $table->string('nickname', 50)->nullable()->comment('');
                $table->index('nickname');

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
