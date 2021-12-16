<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsContent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('cms_content', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->timestamps();

            $table->integer('catId')->nullable()->comment('');
            $table->integer('modelId')->nullable()->comment('模型ID');
            $table->string('alias', 16)->nullable()->comment('别名');
            $table->string('title', 200)->nullable()->comment('标题');
            $table->string('summary', 200)->nullable()->comment('摘要');
            $table->string('cover', 200)->nullable()->comment('封面');

            $table->string('seoTitle', 200)->nullable()->comment('');
            $table->string('seoDescription', 200)->nullable()->comment('');
            $table->string('seoKeywords', 200)->nullable()->comment('');

            $table->dateTime('postTime')->nullable()->comment('发布时间');

            $table->integer('wordCount')->nullable()->comment('文字数');
            $table->integer('viewCount')->nullable()->comment('阅读数');

            /** @see CmsModelContentStatus */
            $table->tinyInteger('status')->nullable()->comment('状态');

            $table->integer('commentCount')->nullable()->comment('评论数');
            $table->integer('likeCount')->nullable()->comment('喜欢数');
            $table->tinyInteger('isRecommend')->nullable()->comment('推荐');
            $table->tinyInteger('isTop')->nullable()->comment('置顶');

            $table->string('tags', 200)->nullable()->comment('标签');
            $table->string('author', 20)->nullable()->comment('作者');
            $table->string('source', 20)->nullable()->comment('来源');

            $table->index(['postTime']);
            $table->index(['catId']);
            $table->unique(['alias']);

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
