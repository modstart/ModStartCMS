<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class NavNavEnable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nav', function (Blueprint $table) {
            $table->tinyInteger('enable')->nullable()->comment('启用');
        });
        \ModStart\Core\Dao\ModelUtil::updateAll('nav', ['enable' => true]);
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
