<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CmsFeatureMemberPost extends Migration
{
    public function up()
    {
        Schema::table('cms_cat', function (Blueprint $table) {
            $table->tinyInteger('memberUserPostEnable')->nullable()->comment('');
            $table->tinyInteger('postMemberGroupEnable')->nullable()->comment('');
            $table->string('postMemberGroups', 100)->nullable()->comment('');
            $table->tinyInteger('postMemberVipEnable')->nullable()->comment('');
            $table->string('postMemberVips', 100)->nullable()->comment('');
        });
        Schema::table('cms_content', function (Blueprint $table) {
            $table->integer('memberUserId')->nullable()->comment('');
            /** @see \Module\Cms\Type\CmsContentVerifyStatus */
            $table->tinyInteger('verifyStatus')->nullable()->comment('');
            $table->index(['memberUserId']);
        });
        \ModStart\Core\Dao\ModelUtil::updateAll('cms_content', [
            'verifyStatus' => \Module\Cms\Type\CmsContentVerifyStatus::VERIFY_PASS,
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
