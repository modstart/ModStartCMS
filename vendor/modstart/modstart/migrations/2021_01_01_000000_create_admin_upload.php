<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminUpload extends Migration
{
    
    public function up()
    {
        Schema::create('admin_upload', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('userId')->nullable()->comment('用户ID');
            $table->string('category', 10)->nullable()->comment('大类');
            $table->integer('dataId')->nullable()->comment('文件ID');
            $table->integer('uploadCategoryId')->nullable()->comment('分类ID');

            $table->index(['uploadCategoryId']);
            $table->index(['userId', 'category']);

        });

        Schema::create('admin_upload_category', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('userId')->nullable()->comment('用户ID');
            $table->string('category', 10)->nullable()->comment('大类');
            $table->integer('pid')->nullable()->comment('上级分类');
            $table->integer('sort')->nullable()->comment('排序');
            $table->string('title', 50)->nullable()->comment('名称');

            $table->index(['userId', 'category']);

        });
    }

    
    public function down()
    {
        
    }
}
