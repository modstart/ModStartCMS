<?php

namespace Module\CmsMemberPost\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminConfigBuilder;

class ConfigController extends Controller
{
    public function index(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('用户投稿设置');
        $builder->switch('CmsMemberPost_Enable', '开启CMS用户投稿');
        $builder->formClass('wide');
        return $builder->perform();
    }

}
