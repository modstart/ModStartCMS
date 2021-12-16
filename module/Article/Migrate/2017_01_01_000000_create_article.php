<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!\ModStart\Core\Dao\ModelManageUtil::hasTable('article')) {
            Schema::create('article', function (Blueprint $table) {

                $table->increments('id');
                $table->timestamps();

                $table->string('position', 50)->nullable()->comment('位置');

                $table->string('title', 200)->nullable()->comment('标题');
                $table->text('content')->nullable()->comment('内容');

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
