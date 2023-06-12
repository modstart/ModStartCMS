<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberUpload extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!\ModStart\Core\Dao\ModelManageUtil::hasTable('member_upload')) {
            Schema::create('member_upload', function (Blueprint $table) {

                $table->increments('id');
                $table->timestamps();
                $table->unsignedInteger('userId')->nullable()->comment('用户ID');
                $table->string('category', 10)->nullable()->comment('大类');
                $table->unsignedInteger('dataId')->nullable()->comment('文件ID');

                $table->integer('uploadCategoryId')->nullable()->comment('分类ID');

                $table->index(['userId', 'uploadCategoryId']);
                $table->index(['dataId']);

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
