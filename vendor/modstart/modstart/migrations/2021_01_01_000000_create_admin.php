<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdmin extends Migration
{
    
    public function up()
    {
        $connection = config('modstart.admin.database.connection') ?: config('database.default');

        Schema::connection($connection)->create('admin_role', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name', 200)->comment('')->nullable();
        });

        Schema::connection($connection)->create('admin_role_rule', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->unsignedInteger('roleId')->comment('')->nullable();
            $table->string('rule', 200)->comment('')->nullable();

            $table->index('roleId');
        });
        Schema::connection($connection)->create('admin_user', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('username', 100)->comment('')->nullable();
            $table->char('password', 32)->comment('')->nullable();
            $table->char('passwordSalt', 16)->comment('')->nullable();
            $table->boolean('ruleChanged')->comment('')->nullable();
            $table->timestamp('lastLoginTime')->comment('')->nullable();
            $table->string('lastLoginIp', 20)->comment('')->nullable();
            $table->timestamp('lastChangePwdTime')->comment('')->nullable();

            $table->unique('username');
        });
        Schema::connection($connection)->create('admin_user_role', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->unsignedInteger('userId')->nullable();
            $table->unsignedInteger('roleId')->nullable();
            $table->index('userId');
            $table->index('roleId');
        });

        Schema::create('admin_log', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('adminUserId')->nullable()->comment('用户ID');
            
            $table->tinyInteger('type')->nullable()->comment('类型');
            $table->string('summary', 400)->nullable()->comment('摘要');
        });

        Schema::create('admin_log_data', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->text('content')->nullable()->comment('内容');
        });

    }

    
    public function down()
    {
        
    }
}
