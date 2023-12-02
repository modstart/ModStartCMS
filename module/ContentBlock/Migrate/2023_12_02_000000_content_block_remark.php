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

            $table->string('text1', 100)->nullable()->comment('');
            $table->string('text2', 100)->nullable()->comment('');
            $table->string('images', 1000)->nullable()->comment('');
            $table->string('summary', 400)->nullable()->comment('');
            $table->string('remark', 100)->nullable()->comment('');

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
