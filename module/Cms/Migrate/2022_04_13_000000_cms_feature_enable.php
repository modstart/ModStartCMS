<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CmsFeatureEnable extends Migration
{
    public function up()
    {
        Schema::table('cms_cat', function (Blueprint $table) {
            $table->tinyInteger('enable')->nullable()->comment('');
        });
        \ModStart\Core\Dao\ModelUtil::updateAll('cms_cat', [
            'enable' => true,
        ]);
        Schema::table('cms_model', function (Blueprint $table) {
            $table->tinyInteger('enable')->nullable()->comment('');
        });
        \ModStart\Core\Dao\ModelUtil::updateAll('cms_model', [
            'enable' => true,
        ]);
        Schema::table('cms_model_field', function (Blueprint $table) {
            $table->tinyInteger('enable')->nullable()->comment('');
        });
        \ModStart\Core\Dao\ModelUtil::updateAll('cms_model_field', [
            'enable' => true,
        ]);

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
