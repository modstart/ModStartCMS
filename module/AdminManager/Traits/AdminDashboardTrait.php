<?php


namespace Module\AdminManager\Traits;

use ModStart\Admin\Layout\AdminPage;
use ModStart\Admin\Widget\SecurityTooltipBox;
use ModStart\Layout\Row;
use Module\AdminManager\Widget\ServerInfoWidget;
use Module\Vendor\Admin\Config\AdminWidgetDashboard;


trait AdminDashboardTrait
{
    public function dashboard()
    {
        
        $page = app(AdminPage::class);
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
