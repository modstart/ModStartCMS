<?php

namespace Module\Survey\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminConfigBuilder;

class ConfigController extends Controller
{
    public function index(AdminConfigBuilder $builder)
    {
        $builder->pageTitle('问卷调查设置');
        $builder->switch('Survey_Enable', '开启问卷调查');
        return $builder->perform();
    }

}
