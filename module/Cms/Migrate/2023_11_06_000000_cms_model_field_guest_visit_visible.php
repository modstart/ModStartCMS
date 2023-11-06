<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CmsModelFieldGuestVisitVisible extends Migration
{
    public function up()
    {
        Schema::table('cms_model_field', function (Blueprint $table) {
            $table->tinyInteger('guestVisitVisible')->nullable()->comment('');
        });
        \ModStart\Core\Dao\ModelUtil::updateAll('cms_model_field', [
            'enable' => false,
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
