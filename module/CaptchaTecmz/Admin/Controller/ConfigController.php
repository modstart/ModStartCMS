<?php

namespace Module\CaptchaTecmz\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminConfigBuilder;
use Module\Vendor\Tecmz\TecmzUtil;

class ConfigController extends Controller
{
    public function setting(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('智能验证码');
        $builder->text('CaptchaTecmz_AppId', 'AppId');
        $builder->text('CaptchaTecmz_AppSecret', 'AppSecret');
        $builder->display('_', '')->addable(true)
            ->help('<div>访问 <a href="' . TecmzUtil::url('Captcha') . '" target="_blank">' . TecmzUtil::url('Captcha') . '</a> 申请</div>');
        return $builder->perform();
    }

}
