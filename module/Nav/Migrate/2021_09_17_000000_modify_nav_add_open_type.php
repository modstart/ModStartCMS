<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use ModStart\Core\Dao\ModelManageUtil;

class ModifyNavAddOpenType extends Migration
{
    
    public function up()
    {
        Schema::table('nav', function (Blueprint $table) {
            
            $table->tinyInteger('openType')->nullable()->comment('');
        });
        \ModStart\Core\Dao\ModelUtil::updateAll('nav', ['openType' => \Module\Nav\Type\NavOpenType::CURRENT_WINDOW]);
    }

    
    public function down()
    {
    }
}
