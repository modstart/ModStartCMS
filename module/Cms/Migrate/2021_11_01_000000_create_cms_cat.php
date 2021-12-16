<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsCat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('cms_cat', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('pid')->nullable()->comment('');
            $table->integer('sort')->nullable()->comment('');

            $table->string('title', 50)->nullable()->comment('');
            $table->string('subTitle', 100)->nullable()->comment('');
            $table->string('bannerBg', 200)->nullable()->comment('');
            $table->string('url', 50)->nullable()->comment('');
            $table->integer('modelId')->nullable()->comment('');

            $table->string('listTemplate', 100)->nullable()->comment('');
            $table->string('detailTemplate', 100)->nullable()->comment('');

            $table->string('seoTitle', 200)->nullable()->comment('');
            $table->string('seoDescription', 200)->nullable()->comment('');
            $table->string('seoKeywords', 200)->nullable()->comment('');

            $table->string('icon', 200)->nullable()->comment('');
            $table->string('cover', 200)->nullable()->comment('');

            $table->tinyInteger('visitMemberGroupEnable')->nullable()->comment('');
            $table->string('visitMemberGroups', 100)->nullable()->comment('');
            $table->tinyInteger('visitMemberVipEnable')->nullable()->comment('');
            $table->string('visitMemberVips', 100)->nullable()->comment('');

            $table->unique(['url']);

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
