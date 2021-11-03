<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsChannel extends Migration
{
    
    public function up()
    {

        Schema::create('cms_channel', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('pid')->nullable()->comment('用户');
            $table->integer('sort')->nullable()->comment('用户');

            $table->string('alias', 16)->nullable()->comment('Alias');

            $table->string('cover', 200)->nullable()->comment('封面');
            $table->string('title', 50)->nullable()->comment('标题');
            $table->string('description', 400)->nullable()->comment('公告');

            $table->tinyInteger('pushEnable')->nullable()->comment('允许投稿');
            $table->tinyInteger('pushVerify')->nullable()->comment('投稿是否需要审核');

            $table->integer('postCount')->nullable()->comment('文章数');

            $table->unique(['alias']);

        });


        Schema::create('cms_channel_post', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('channelId')->nullable()->comment('专题');
            $table->integer('postId')->nullable()->comment('文章');

            $table->index(['channelId']);
            $table->index(['postId']);

        });


        Schema::create('cms_channel_post_apply', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('channelId')->nullable()->comment('专题');
            $table->integer('postId')->nullable()->comment('文章');

            $table->index(['channelId']);
            $table->index(['postId']);

        });

    }

    
    public function down()
    {

    }
}
