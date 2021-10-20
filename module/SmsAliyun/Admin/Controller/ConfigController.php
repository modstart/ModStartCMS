<?php

namespace Module\SmsAliyun\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminConfigBuilder;
use Module\SmsAliyun\Provider\SmsSender;
use Module\Vendor\Provider\SmsTemplate\SmsTemplateProvider;

class ConfigController extends Controller
{
    public function setting(AdminConfigBuilder $builder)
    {
        $builder->pageTitle(SmsSender::MODULE_TITLE . '短信设置');
        $builder->switch(SmsSender::MODULE_NAME . '_Enable', '开启短信发送')
            ->help('<div>访问 <a href="' . SmsSender::MODULE_URL . '" target="_blank">' . SmsSender::MODULE_TITLE . '</a> 申请</div>');
        $builder->text(SmsSender::MODULE_NAME . '_AccessKeyId', 'AccessKeyId');
        $builder->text(SmsSender::MODULE_NAME . '_AccessKeySecret', 'accessKeySecret');
        $builder->text(SmsSender::MODULE_NAME . '_SignName', '短信签名');
        foreach (SmsTemplateProvider::map() as $name => $info) {
            $builder->text(SmsSender::MODULE_NAME . '_Template_' . $name, $info['title'] . '模板ID')
                ->help($info['description'] . '，示例：<code>' . $info['example'] . '</code>');
        }
        $builder->formClass('wide');
        return $builder->perform();
    }

}
