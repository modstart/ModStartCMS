<?php

namespace Module\SmsTecmz\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminConfigBuilder;
use Module\Vendor\Provider\SmsTemplate\SmsTemplateProvider;
use Module\Vendor\Sms\SmsUtil;
use Module\Vendor\Tecmz\TecmzUtil;

class ConfigController extends Controller
{
    public function setting(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('魔众短信');
        $builder->switch('SmsTecmz_Enable', '开启短信发送')->help('<div>访问 <a href="' . TecmzUtil::url() . '" target="_blank">' . TecmzUtil::url() . '</a> 申请</div>');
        $builder->text('SmsTecmz_AppId', 'AppId');
        $builder->text('SmsTecmz_AppSecret', 'AppSecret');
        foreach (SmsTemplateProvider::map() as $name => $info) {
            $builder->text('SmsTecmz_Template_' . $name, $info['title'] . '模板ID')
                ->help($info['description'] . '，示例：<code>' . $info['example'] . '</code>');
        }
        $builder->formClass('wide');
        return $builder->perform();
    }

}
