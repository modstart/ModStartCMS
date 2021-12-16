<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use ModStart\Core\Dao\ModelManageUtil;

class CreateBanner extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (ModelManageUtil::hasTable('banner')) {
            $columns = ModelManageUtil::listTableColumns('banner');
            if (!in_array('position', $columns)) {
                Schema::table('banner', function (Blueprint $table) {
                    $table->string('position', 50)->nullable()->comment('位置');
                });
            }
            if (!in_array('sort', $columns)) {
                Schema::table('banner', function (Blueprint $table) {
                    $table->integer('sort')->nullable()->comment('顺序');
                });
            }
        } else {
            Schema::create('banner', function (Blueprint $table) {

                $table->increments('id');
                $table->timestamps();

                $table->string('position', 50)->nullable()->comment('位置');

                $table->integer('sort')->nullable()->comment('顺序');
                $table->string('image', 100)->nullable()->comment('图片');
                $table->string('link', 200)->nullable()->comment('链接');

            });
        }
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
