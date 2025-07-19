<?php

namespace Module\VisitStatistic\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminConfigBuilder;

class ConfigController extends Controller
{
    public static $PermitMethodMap = [
        'index' => '\\Module\\VisitStatistic\\Admin\\Controller\\VisitStatisticReportController@index'
    ];

    public function index(AdminConfigBuilder $builder)
    {
        $builder->switch('VisitStatistic_Enable', '开启动态访问记录')
            ->help('开启后将记录网站动态请求访问记录，可在访问记录中查看');
        $builder->switch('VisitStatistic_TickEnable', '开启静态访问记录')
            ->help('开启后将开启网站的静态访问记录，需要在访问的静态页面请求 /visit_statistic/tick 路径，通常在网站底部引入即可');
        $builder->number('VisitStatistic_MaxHistoryDay', '历史数据保持天数')
            ->help('越长所需数据库存储越大')
            ->defaultValue(15);
        $builder->switch('VisitStatistic_UaDisable', '不记录UserAgent')
            ->help('开启后不记录UserAgent，节省数据库空间');
        $builder->switch('VisitStatistic_IgnoreRobot', '忽略搜索引擎')
            ->help('开启后不记录搜索引擎的访问记录');
        $builder->formClass('wide');
        $builder->useDialog();
        $builder->pageTitle('网站访问记录设置');
        return $builder->perform();
    }

}
