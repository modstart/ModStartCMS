<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ModifyAdminUserAddPhoneEmail extends Migration
{
    
    public function up()
    {
        $connection = config('modstart.admin.database.connection') ?: config('database.default');

        Schema::connection($connection)->table('admin_user', function (Blueprint $table) {
            $table->string('phone', 11)->comment('')->nullable();
            $table->string('email', 100)->comment('')->nullable();
            $table->unique('phone');
            $table->unique('email');
        });

    }

    
    public function down()
    {
    }
}
