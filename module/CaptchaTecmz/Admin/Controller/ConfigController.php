<?php

namespace Module\CaptchaTecmz\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminConfigBuilder;
use Module\Vendor\Tecmz\TecmzUtil;

class ConfigController extends Controller
{
    public function setting(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('魔众智能验证码');
        $builder->display('CaptchaTecmz_Remark', '说明')->addable(true)
            ->help('<div>访问 <a href="' . TecmzUtil::url() . '" target="_blank">' . TecmzUtil::url() . '</a> 申请</div>');
        $builder->text('CaptchaTecmz_AppId', 'AppId');
        $builder->text('CaptchaTecmz_AppSecret', 'AppSecret');
        return $builder->perform();
    }

}
