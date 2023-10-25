<?php


namespace Module\Member\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Admin\Widget\DashboardItemA;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\ColorUtil;
use ModStart\Core\Util\TimeUtil;
use ModStart\Layout\Row;
use ModStart\Widget\Box;
use ModStart\Widget\Chart\Line;

class MemberDashboardController extends Controller
{
    public function index(AdminPage $page)
    {
        $page->pageTitle('用户统计');
        $report = [];
        $report['yesterdayCount'] = ModelUtil::model('member_user')
            ->where('created_at', '>=', TimeUtil::yesterdayStart())
            ->where('created_at', '<=', TimeUtil::yesterdayEnd())
            ->where(['isDeleted' => false])
            ->count();

        $w = date('w');
        if ($w == 0) {
            $w = 7;
        }
        $lastWeek = time() - TimeUtil::PERIOD_DAY * 7 - TimeUtil::PERIOD_DAY * ($w - 1);
        $report['lastWeekCount'] = ModelUtil::model('member_user')
            ->where('created_at', '>=', date('Y-m-d 00:00:00', $lastWeek))
            ->where('created_at', '<=', date('Y-m-d 23:59:59', $lastWeek + TimeUtil::PERIOD_DAY * 7))
            ->where(['isDeleted' => false])
            ->count();
        $page->row(function (Row $row) use ($report) {
            $row->column(4, DashboardItemA::makeIconNumberTitle(
                'iconfont icon-user', ModelUtil::count('member_user'), '用户总数',
                modstart_admin_url('member'), ColorUtil::randomColor()
            ));
            $row->column(4, DashboardItemA::makeIconNumberTitle(
                'iconfont icon-user', $report['yesterdayCount'], '昨日增长',
                modstart_admin_url('member'), ColorUtil::randomColor()
            ));
            $row->column(4, DashboardItemA::makeIconNumberTitle(
                'iconfont icon-user', $report['lastWeekCount'], '上周增长',
                modstart_admin_url('member'), ColorUtil::randomColor()
            ));
        });
        $page->append(Box::make(Line::make()->tableDailyCountLatest(
            [
                ['title' => '总数', 'table' => 'member_user', 'where' => ['isDeleted' => false]],
            ],
            30
        ), '会员增长趋势'));
        return $page;
    }
}
