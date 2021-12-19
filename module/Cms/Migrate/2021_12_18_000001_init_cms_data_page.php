<?php

use Illuminate\Database\Migrations\Migration;
use Module\Cms\Type\CmsMode;
use Module\Cms\Util\CmsCatUtil;
use Module\Cms\Util\CmsModelUtil;

class InitCmsDataPage extends Migration
{
    public function up()
    {
        CmsModelUtil::build([
            'name' => 'page',
            'title' => '单页内容',
            'mode' => CmsMode::PAGE,
            'pageTemplate' => 'default.blade.php',
        ]);
        CmsCatUtil::build('page', [
            'url' => 'about',
            'title' => '关于我们',
        ]);
        CmsCatUtil::build('page', [
            'url' => 'contact',
            'title' => '联系我们',
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
