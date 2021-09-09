<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use ModStart\Core\Dao\ModelUtil;
use Module\Banner\Type\BannerType;

class ModifyBannerAddVideo extends Migration
{
    
    public function up()
    {
        Schema::table('banner', function (Blueprint $table) {
            $table->string('video', 200)->nullable()->comment('');
        });
    }

    
    public function down()
    {
    }
}
