<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class SurveyCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_activity', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->string('template', 50)->nullable()->comment('模板');
            $table->string('alias', 8)->nullable()->comment('Hash');

            $table->tinyInteger('enable')->nullable()->comment('开启');

            $table->tinyInteger('loginRequired')->nullable()->comment('需要登录');

            /** @see \Module\Survey\Type\JoinType */
            $table->tinyInteger('joinType')->nullable()->comment('参加限制');

            $table->string('name', 200)->nullable()->comment('名称');
            $table->timestamp('startTime')->nullable()->comment('开始时间');
            $table->timestamp('endTime')->nullable()->comment('结束时间');
            $table->string('cover', 200)->nullable()->comment('封面');
            $table->text('description')->nullable()->comment('说明');

            $table->unique('alias');

        });

        Schema::create('survey_question', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('activityId')->nullable()->comment('活动');

            $table->integer('sort')->nullable()->comment('排序');

            $table->tinyInteger('required')->nullable()->comment('必答题');

            /** @see \Module\Survey\Type\SurveyQuestionType */
            $table->tinyInteger('type')->nullable()->comment('题目类型');

            $table->string('body', 10000)->nullable()->comment('内容');
            $table->string('choice', 10000)->nullable()->comment('选项');

            $table->index(['activityId', 'sort']);

        });

        Schema::create('survey_answer', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('activityId')->nullable()->comment('活动');
            $table->integer('memberUserId')->nullable()->comment('用户');

            $table->index('memberUserId');
            $table->index('activityId');

        });

        Schema::create('survey_answer_item', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('activityId')->nullable()->comment('活动');
            $table->integer('memberUserId')->nullable()->comment('用户');
            $table->integer('answerId')->nullable()->comment('回答');

            $table->integer('questionId')->nullable()->comment('问题');
            /** @see \Module\Survey\Type\SurveyQuestionType */
            $table->integer('questionType')->nullable()->comment('问题类型');
            $table->string('body', 10000)->nullable()->comment('内容');

            $table->index('activityId');
            $table->index('answerId');

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
