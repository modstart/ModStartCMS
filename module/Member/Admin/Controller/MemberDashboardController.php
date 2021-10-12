<?php


namespace Module\Member\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Admin\Widget\DashboardItemA;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
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
        $page->row(function (Row $row) {
            $row->column(6, DashboardItemA::makeIconNumberTitle(
                'iconfont icon-user', ModelUtil::count('member_user'), '用户总数',
                modstart_admin_url('member'), ColorUtil::randomColor()
            ));
            $row->column(6, DashboardItemA::makeIconNumberTitle(
                'iconfont icon-user',
                ModelUtil::model('member_user')
                    ->where('created_at', '>=', TimeUtil::yesterdayStart())
                    ->where('created_at', '<=', TimeUtil::yesterdayEnd())
                    ->count(),
                '昨日增长',
                modstart_admin_url('member'), ColorUtil::randomColor()
            ));
        });
        $page->append(Box::make(Line::make()->tableDailyCountLatest(
            [
                ['title' => '总数', 'table' => 'member_user'],
            ]
        ), '会员增长趋势'));
        return $page;
    }
}
