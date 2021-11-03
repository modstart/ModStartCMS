<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsMemberPost extends Migration
{
    
    public function up()
    {
        Schema::create('cms_member_post_category', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('memberUserId')->nullable()->comment('用户ID');
            $table->string('title', 100)->nullable()->comment('文章分类');

            $table->index(['memberUserId']);
        });

        Schema::create('cms_member_post', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('memberUserId')->nullable()->comment('用户ID');
            $table->integer('categoryId')->nullable()->comment('分类');
            $table->string('title', 200)->nullable()->comment('标题');

            $table->tinyInteger('isPublished')->nullable()->comment('已发布');

            
            $table->tinyInteger('contentType')->nullable()->comment('类型');
            $table->text('content')->nullable()->comment('内容');

            $table->tinyInteger('isOriginal')->nullable()->comment('原创');
            $table->string('tags', 200)->nullable()->comment('标签');

            $table->index(['memberUserId']);
            $table->index(['categoryId']);
        });

        Schema::create('cms_member_post_history', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('memberPostId')->nullable()->comment('文章');

            $table->string('title', 200)->nullable()->comment('标题');
            $table->text('content')->nullable()->comment('内容');

        });

    }

    
    public function down()
    {

    }
}
