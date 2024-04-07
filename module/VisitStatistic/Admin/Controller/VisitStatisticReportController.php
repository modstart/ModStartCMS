<?php

namespace Module\VisitStatistic\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminPage;
use Module\VisitStatistic\Admin\Widget\VisitStatisticReport;

class VisitStatisticReportController extends Controller
{
    public function index(AdminPage $page)
    {
        $page->pageTitle('网站访问');
        $page->append(new VisitStatisticReport());
        return $page;
    }
}
