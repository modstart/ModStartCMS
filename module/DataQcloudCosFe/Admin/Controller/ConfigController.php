<?php

namespace Module\DataQcloudCosFe\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminConfigBuilder;

class ConfigController extends Controller
{
    public function index(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('腾讯云COS(前端直传)');
        $builder->switch('DataQcloudCosFe_Enable', '启用腾讯云COS');
        $builder->text('DataQcloudCosFe_Domain', '腾讯云COS域名')
            ->help('如 https://xxx.cos.ap-xxx.myqcloud.com');
        $builder->select('DataQcloudCosFe_Region', 'Region')->options([
            'ap-nanjing' => '南京',
            'ap-chengdu' => '成都',
            'ap-beijing' => '北京',
            'ap-guangzhou' => '广州',
            'ap-shanghai' => '上海',
            'ap-chongqing' => '重庆',
            'ap-beijing-fsi' => '北京金融',
            'ap-shanghai-fsi' => '上海金融',
            'ap-shenzhen-fsi' => '深圳金融',
            'ap-hongkong' => '香港',
            'ap-jakarta' => '印尼雅加达',
            'sa-saopaulo' => '巴西圣保罗',
            'ap-singapore' => '新加坡',
            'ap-mumbai' => '印度孟买',
            'ap-seoul' => '韩国首尔',
            'ap-bangkok' => '泰国曼谷',
            'ap-tokyo' => '日本东京',
            'eu-frankfurt' => '德国法兰克福',
            'na-toronto' => '加拿大多伦多',
            'na-ashburn' => '美东弗吉尼亚',
            'na-siliconvalley' => '美西硅谷',
        ]);
        $builder->text('DataQcloudCosFe_SecretId', 'SecretId');
        $builder->text('DataQcloudCosFe_SecretKey', 'SecretKey');
        $builder->text('DataQcloudCosFe_Bucket', 'Bucket');
        $builder->formClass('wide');
        return $builder->perform();
    }
}
