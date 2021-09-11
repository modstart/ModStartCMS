<?php

namespace Module\SiteCounter\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminConfigBuilder;

class ConfigController extends Controller
{
    public function setting(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('访问设置');
        $builder->textarea('systemCounter', 'head访问统计代码');
        $builder->textarea('systemCounterBody', 'body访问统计代码');
        $builder->formClass('wide');
        return $builder->perform();
    }

}
