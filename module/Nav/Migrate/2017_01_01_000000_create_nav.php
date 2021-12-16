<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use ModStart\Core\Dao\ModelManageUtil;

class CreateNav extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (ModelManageUtil::hasTable('nav')) {

            $columns = ModelManageUtil::listTableColumns('nav');
            if (!in_array('position', $columns)) {
                if (in_array('type', $columns)) {
                    Schema::table('nav', function (Blueprint $table) {
                        $table->renameColumn('type', 'position');
                    });
                } else {
                    Schema::table('nav', function (Blueprint $table) {
                        $table->string('position', 50)->nullable()->comment('位置');
                    });
                }
            }
            if (!in_array('name', $columns)) {
                if (in_array('title', $columns)) {
                    Schema::table('nav', function (Blueprint $table) {
                        $table->renameColumn('title', 'name');
                    });
                } else {
                    Schema::table('nav', function (Blueprint $table) {
                        $table->string('name', 100)->nullable()->comment('图片');
                    });
                }
            }
            if (!in_array('link', $columns)) {
                if (in_array('url', $columns)) {
                    Schema::table('nav', function (Blueprint $table) {
                        $table->renameColumn('url', 'link');
                    });
                } else {
                    Schema::table('nav', function (Blueprint $table) {
                        $table->string('link', 200)->nullable()->comment('链接');
                    });
                }
            }

        } else {
            Schema::create('nav', function (Blueprint $table) {

                $table->increments('id');
                $table->timestamps();

                $table->string('position', 50)->nullable()->comment('位置');

                $table->integer('sort')->nullable()->comment('顺序');
                $table->string('name', 100)->nullable()->comment('图片');
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