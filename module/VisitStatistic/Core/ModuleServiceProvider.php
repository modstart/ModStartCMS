<?php

namespace Module\VisitStatistic\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use ModStart\Admin\Layout\AdminPage;
use ModStart\App\Core\CurrentApp;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Events\ModStartRequestHandled;
use ModStart\Core\Input\Request;
use ModStart\Core\Util\AgentUtil;
use ModStart\Core\Util\RandomUtil;
use ModStart\Core\Util\StrUtil;
use ModStart\Widget\Box;
use ModStart\Widget\Chart\Line;
use Module\Vendor\Admin\Widget\AdminWidgetDashboard;
use Module\VisitStatistic\Model\VisitStatisticDailyReport;
use Module\VisitStatistic\Model\VisitStatisticItem;
use Module\VisitStatistic\Type\VisitStatisticDevice;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        if (modstart_config('VisitStatistic_Enable', false)) {
            Event::listen(ModStartRequestHandled::class, function (ModStartRequestHandled $e) {
                if (CurrentApp::is(CurrentApp::ADMIN) || !$e->isGet() || !$e->isHtml()) {
                    return;
                }
                $userAgent = AgentUtil::getUserAgent();
                if (empty($userAgent)) {
                    return;
                }
                if (modstart_config('VisitStatistic_IgnoreRobot', false)) {
                    $robot = AgentUtil::detectRobot($userAgent);
                    if ($robot) {
                        return;
                    }
                }
                $data = [];
                $data['url'] = $e->url;
                if (strlen($data['url']) > 200) {
                    $data['url'] = substr($data['url'], 0, 200);
                }
                $data['ip'] = Request::ip();
                $data['device'] = VisitStatisticDevice::current();
                if (!modstart_config('VisitStatistic_UaDisable', false)) {
                    $data['ua'] = StrUtil::mbLimit($userAgent, 200);
                }
                try {
                    ModelUtil::insert('visit_statistic_item', $data);
                } catch (\Exception $e) {
                }
                if (RandomUtil::percent(10)) {
                    $days = modstart_config('VisitStatistic_MaxHistoryDay', 15);
                    try {
                        VisitStatisticItem::deleteHistory($days);
                    } catch (\Exception $e) {
                    }
                }
            });

            AdminWidgetDashboard::registerFoot(function (AdminPage $page) {
                $data = VisitStatisticDailyReport::report();
                $line = Line::make()->xData($data['time'])
                    ->ySeries(0, $data['pv'], '访问量', ['lineColor' => '#4F7FF3'])
                    ->ySeries(1, $data['uv'], '访客数', ['lineColor' => '#6A46BD']);
                $page->row(Box::make($line, '<i class="fa fa-bar-chart"></i> 访问统计'));
            });

        }

        AdminMenu::register(function () {
            return [
                'title' => '内容管理',
                'icon' => 'file',
                'sort' => 150,
                'children' => [
                    [
                        'title' => '网站访问记录',
                        'url' => '\Module\VisitStatistic\Admin\Controller\VisitStatisticItemController@index',
                    ]
                ]
            ];
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
