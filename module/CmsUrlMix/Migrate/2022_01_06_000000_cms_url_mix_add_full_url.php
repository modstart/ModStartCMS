<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CmsUrlMixAddFullUrl extends Migration
{
    public function up()
    {
        Schema::table('cms_cat', function (Blueprint $table) {
            $table->string('fullUrl', 50)->nullable()->comment('');
            $table->string('pageFullUrl', 50)->nullable()->comment('');
        });
        Schema::table('cms_content', function (Blueprint $table) {
            $table->string('fullUrl', 50)->nullable()->comment('');
            $table->index('fullUrl');
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
