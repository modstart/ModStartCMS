<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ContentBlockRemark extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('content_block', function (Blueprint $table) {

            $table->string('remark', 100)->nullable()->comment('');
            $table->text('basicTexts')->nullable()->comment('');
            $table->text('basicImages')->nullable()->comment('');

        });

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
