<?php

use Illuminate\Database\Migrations\Migration;
use Module\Cms\Type\CmsMode;
use Module\Cms\Util\CmsCatUtil;
use Module\Cms\Util\CmsModelUtil;

class InitCmsDataForm extends Migration
{
    public function up()
    {
        CmsModelUtil::build([
            'name' => 'message',
            'title' => '留言本',
            'mode' => CmsMode::FORM,
            'formTemplate' => 'default.blade.php',
        ], [
            [
                'name' => 'phone',
                'title' => '手机',
                'fieldType' => 'text',
                'isRequired' => false,
            ],
            [
                'name' => 'name',
                'title' => '姓名',
                'fieldType' => 'text',
                'isRequired' => false,
            ],
        ]);
        CmsCatUtil::build('message', [
            'url' => 'message',
            'title' => '留言本',
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
