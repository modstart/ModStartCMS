<?php

use Illuminate\Database\Migrations\Migration;
use ModStart\Core\Dao\ModelUtil;

class UpgradeAdminUploadCategoryId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        ModelUtil::update('admin_upload', [
            'uploadCategoryId' => 0,
        ], [
            'uploadCategoryId' => -1,
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
