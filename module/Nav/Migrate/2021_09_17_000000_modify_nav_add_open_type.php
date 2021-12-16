<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use ModStart\Core\Dao\ModelManageUtil;

class ModifyNavAddOpenType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nav', function (Blueprint $table) {
            /** @see \Module\Nav\Type\NavOpenType */
            $table->tinyInteger('openType')->nullable()->comment('');
        });
        \ModStart\Core\Dao\ModelUtil::updateAll('nav', ['openType' => \Module\Nav\Type\NavOpenType::CURRENT_WINDOW]);
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
