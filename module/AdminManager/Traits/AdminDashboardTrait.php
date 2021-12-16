<?php


namespace Module\AdminManager\Traits;

use ModStart\Admin\Layout\AdminPage;
use ModStart\Admin\Widget\SecurityTooltipBox;
use ModStart\Layout\Row;
use Module\AdminManager\Widget\ServerInfoWidget;
use Module\Vendor\Admin\Config\AdminWidgetDashboard;

/**
 * Trait AdminDashboardTrait
 * @package Module\AdminManager\Traits
 * @since 1.5.0
 */
trait AdminDashboardTrait
{
    public function dashboard()
    {
        /** @var AdminPage $page */
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
