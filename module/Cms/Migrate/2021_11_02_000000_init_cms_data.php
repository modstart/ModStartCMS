<?php

use Illuminate\Database\Migrations\Migration;
use Module\Cms\Type\CmsModelFieldType;
use Module\Cms\Util\CmsCatUtil;
use Module\Cms\Util\CmsModelUtil;

class InitCmsData extends Migration
{
    public function up()
    {
        \ModStart\Core\Exception\BizException::throws('a');
    }

    
    public function down()
    {

    }
}
