<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use ModStart\Core\Dao\ModelUtil;
use Module\Banner\Type\BannerType;

class ModifyBannerAddType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('banner', function (Blueprint $table) {

            /** @see \Module\Banner\Type\BannerType */
            $table->tinyInteger('type')->nullable()->comment('');
            if (!\ModStart\Core\Dao\ModelManageUtil::hasTableColumn('banner', 'title')) {
                $table->string('title', 100)->nullable()->comment('');
            }
            $table->string('slogan', 200)->nullable()->comment('');
            $table->string('linkText', 20)->nullable()->comment('');
            $table->tinyInteger('colorReverse')->nullable()->comment('');

        });

        foreach (ModelUtil::all('banner') as $item) {
            ModelUtil::update('banner', $item['id'], ['type' => BannerType::IMAGE]);
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
