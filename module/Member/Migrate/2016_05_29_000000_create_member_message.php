<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberMessage extends Migration
{
    
    public function up()
    {
        if (!\ModStart\Core\Dao\ModelManageUtil::hasTable('member_message')) {
            Schema::create('member_message', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();

                $table->integer('userId')->comment('用户ID')->nullable();
                $table->tinyInteger('status')->comment('1未读 2已读')->nullable();
                $table->integer('fromId')->comment('来源用户ID')->nullable();
                $table->string('content', 20000)->comment('消息内容(html)')->nullable();

                $table->index(['userId', 'status']);

            });
        }
    }

    
    public function down()
    {
    }
}
