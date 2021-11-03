<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsMemberComment extends Migration
{
    
    public function up()
    {

        Schema::create('cms_member_comment', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->bigInteger('postId')->nullable()->comment('');

            $table->integer('memberUserId')->nullable()->comment('');
            $table->integer('rootCommentId')->nullable()->comment('所属评论');

            $table->integer('likeCount')->nullable()->comment('喜欢数');

            $table->string('content', 1000)->nullable()->comment('内容');

            $table->index(['memberUserId']);
            $table->index(['postId']);

        });

        Schema::create('cms_member_comment_like', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('commentId')->nullable()->comment('评论');
            $table->integer('memberUserId')->nullable()->comment('用户');

            $table->unique(['commentId', 'memberUserId']);

        });


    }

    
    public function down()
    {

    }
}
