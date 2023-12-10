<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MemberUploadType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_upload', function (Blueprint $table) {
            /** @see \ModStart\Admin\Type\UploadType */
            $table->tinyInteger('type')->nullable()->comment('类型');
        });
        \ModStart\Core\Dao\ModelUtil::updateAll('member_upload', [
            'type' => \ModStart\Admin\Type\UploadType::USER
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
