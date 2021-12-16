<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use ModStart\Core\Dao\ModelManageUtil;

class CreatePartner extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (ModelManageUtil::hasTable('partner')) {
            $columns = ModelManageUtil::listTableColumns('partner');
            if (!in_array('position', $columns)) {
                Schema::table('partner', function (Blueprint $table) {
                    $table->string('position', 50)->nullable()->comment('位置');
                });
            }
            if (!in_array('sort', $columns)) {
                Schema::table('partner', function (Blueprint $table) {
                    $table->integer('sort')->nullable()->comment('排序');
                });
            }
        } else {
            Schema::create('partner', function (Blueprint $table) {

                $table->increments('id');
                $table->timestamps();

                $table->string('position', 50)->nullable()->comment('位置');
                $table->integer('sort')->nullable()->comment('排序');

                $table->string('title', 20)->nullable()->comment('名称');
                $table->string('logo', 200)->nullable()->comment('Logo');
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
