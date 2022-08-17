<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ModifyAdminRoleAddRemark extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = config('modstart.admin.database.connection') ?: config('database.default');

        Schema::connection($connection)->table('admin_role', function (Blueprint $table) {
            $table->string('remark', 400)->comment('')->nullable();
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
