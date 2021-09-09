<?php


namespace App\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Admin\Widget\DashboardItemA;
use ModStart\Admin\Widget\SecurityTooltipBox;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\ColorUtil;
use ModStart\Layout\Row;
use Module\AdminManager\Widget\ServerInfoWidget;
use Module\Vendor\Admin\Config\AdminWidgetDashboard;

class IndexController extends Controller
{
    public function index(AdminPage $page)
    {
        $page->pageTitle(L('Dashboard'))
            ->row(new SecurityTooltipBox())
            ->append(new Row(function (Row $row) {
                AdminWidgetDashboard::callIcon($row);
            }));
        AdminWidgetDashboard::call($page);
        $page->append(new ServerInfoWidget());
        return $page;
    }
}
