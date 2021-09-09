<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ChangeCmsMemberUserFields extends Migration
{
    
    public function up()
    {

        Schema::table('member_user', function (Blueprint $table) {

            $table->integer('cmsPostCount')->nullable()->comment('文章数');
            $table->integer('cmsWordCount')->nullable()->comment('文字数');

            
            $table->tinyInteger('cmsEditorType')->nullable()->comment('编辑器类型');

        });

    }

    
    public function down()
    {

    }
}
