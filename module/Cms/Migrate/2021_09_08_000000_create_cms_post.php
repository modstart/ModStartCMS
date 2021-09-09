<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsPost extends Migration
{
    
    public function up()
    {

        Schema::create('cms_post', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->string('alias', 16)->nullable()->comment('Alias');

            $table->integer('channelId')->nullable()->comment('用户');

            $table->integer('memberUserId')->nullable()->comment('用户');
            $table->integer('memberPostId')->nullable()->comment('用户文章');

            $table->integer('wordCount')->nullable()->comment('文字数');
            $table->integer('viewCount')->nullable()->comment('阅读数');

            $table->integer('commentCount')->nullable()->comment('评论数');
            $table->integer('likeCount')->nullable()->comment('喜欢数');

            $table->string('title', 200)->nullable()->comment('标题');
            $table->text('contentHtml')->nullable()->comment('内容');

            $table->tinyInteger('isRecommend')->nullable()->comment('推荐');
            $table->tinyInteger('isDeleted')->nullable()->comment('已删除');

            $table->tinyInteger('isOriginal')->nullable()->comment('原创');
            $table->string('tags', 200)->nullable()->comment('标签');

            $table->unique(['alias']);
            $table->index(['channelId']);
            $table->index(['memberUserId']);
            $table->index(['memberPostId']);

        });

        Schema::create('cms_post_like', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('postId')->nullable()->comment('文章');
            $table->integer('memberUserId')->nullable()->comment('用户');

            $table->unique(['postId', 'memberUserId']);
            $table->index(['memberUserId']);

        });


    }

    
    public function down()
    {

    }
}
