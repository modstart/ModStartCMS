<?php


namespace Module\Member\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Admin\Widget\DashboardItem;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Core\Util\TimeUtil;
use ModStart\Layout\Row;
use ModStart\Widget\Box;
use ModStart\Widget\Chart\Line;
use ModStart\Widget\Nav;
use Module\Member\Admin\Widget\DashboardDaily;
use Module\Member\Model\MemberLoginLog;
use Module\Member\Model\MemberUser;

class MemberDashboardController extends Controller
{
    public function index(AdminPage $page)
    {
        $input = InputPackage::buildFromInput();
        $type = $input->getTrimString('type', 'report');
        $page->pageTitle('用户数据');
        $page->append(Nav::make([
            [
                'title' => '统计报表',
                'url' => CRUDUtil::adminUrlWithTab('member/dashboard', ['type' => 'report']),
                'active' => $type == 'report',
            ],
            [
                'title' => '每日明细',
                'url' => CRUDUtil::adminUrlWithTab('member/dashboard', ['type' => 'daily']),
                'active' => $type == 'daily',
            ]
        ], 'margin-bottom'));

        switch ($type) {
            case 'daily':
                $page->append(new DashboardDaily());
                break;
            default:
                $report = [];
                $report['todayCount'] = MemberUser::query()
                    ->where('created_at', '>=', TimeUtil::todayStart())
                    ->where('created_at', '<=', TimeUtil::todayEnd())
                    ->where(['isDeleted' => false])
                    ->count();
                $report['yesterdayCount'] = MemberUser::query()
                    ->where('created_at', '>=', TimeUtil::yesterdayStart())
                    ->where('created_at', '<=', TimeUtil::yesterdayEnd())
                    ->where(['isDeleted' => false])
                    ->count();
                $report['todayLoginCount'] = MemberLoginLog::query()
                    ->where('created_at', '>=', TimeUtil::todayStart())
                    ->where('created_at', '<=', TimeUtil::todayEnd())
                    ->count();
                $report['yesterdayLoginCount'] = MemberLoginLog::query()
                    ->where('created_at', '>=', TimeUtil::yesterdayStart())
                    ->where('created_at', '<=', TimeUtil::yesterdayEnd())
                    ->count();
                $w = date('w');
                if ($w == 0) {
                    $w = 7;
                }
                $thisWeek = time() - TimeUtil::PERIOD_DAY * ($w - 1);
                $lastWeek = time() - TimeUtil::PERIOD_DAY * 7 - TimeUtil::PERIOD_DAY * ($w - 1);
                $report['thisWeekCount'] = MemberUser::query()
                    ->where('created_at', '>=', date('Y-m-d 00:00:00', $thisWeek))
                    ->where(['isDeleted' => false])
                    ->count();
                $report['lastWeekCount'] = MemberUser::query()
                    ->where('created_at', '>=', date('Y-m-d 00:00:00', $lastWeek))
                    ->where('created_at', '<=', date('Y-m-d 23:59:59', $lastWeek + TimeUtil::PERIOD_DAY * 7))
                    ->where(['isDeleted' => false])
                    ->count();
                $report['thisWeekLoginCount'] = MemberLoginLog::query()
                    ->where('created_at', '>=', date('Y-m-d 00:00:00', $thisWeek))
                    ->count();
                $report['lastWeekLoginCount'] = MemberLoginLog::query()
                    ->where('created_at', '>=', date('Y-m-d 00:00:00', $lastWeek))
                    ->where('created_at', '<=', date('Y-m-d 23:59:59', $lastWeek + TimeUtil::PERIOD_DAY * 7))
                    ->count();

                $page->row(function (Row $row) use ($report) {
                    $row->flexColumn(DashboardItem::makeTitleDataList(
                        'iconfont icon-users',
                        '概况',
                        [
                            [
                                'title' => '用户总数',
                                'value' => ModelUtil::count('member_user'),
                                'url' => modstart_admin_url('member'),
                            ],
                        ]
                    ));
                    $row->flexColumn(DashboardItem::makeTitleDataList(
                        'iconfont icon-users',
                        '注册人数',
                        [
                            [
                                'title' => '今日',
                                'value' => $report['todayCount'],
                                'url' => modstart_admin_url('member'),
                            ],
                            [
                                'title' => '昨日',
                                'value' => $report['yesterdayCount'],
                                'url' => modstart_admin_url('member'),
                            ],
                            [
                                'title' => '本周',
                                'value' => $report['thisWeekCount'],
                                'url' => modstart_admin_url('member'),
                            ],
                            [
                                'title' => '上周',
                                'value' => $report['lastWeekCount'],
                                'url' => modstart_admin_url('member'),
                            ],
                        ]
                    ));
                    $row->flexColumn(DashboardItem::makeTitleDataList(
                        'iconfont icon-users',
                        '登录人次',
                        [
                            [
                                'title' => '今日',
                                'value' => $report['todayLoginCount'],
                                'url' => modstart_admin_url('member'),
                            ],
                            [
                                'title' => '昨日',
                                'value' => $report['yesterdayLoginCount'],
                                'url' => modstart_admin_url('member'),
                            ],
                            [
                                'title' => '本周',
                                'value' => $report['thisWeekLoginCount'],
                                'url' => modstart_admin_url('member'),
                            ],
                            [
                                'title' => '上周',
                                'value' => $report['lastWeekLoginCount'],
                                'url' => modstart_admin_url('member'),
                            ],
                        ]
                    ));
                });
                $page->append(Box::make(Line::make()->tableDailyCountLatest(
                    [
                        ['title' => '总数', 'table' => 'member_user', 'where' => ['isDeleted' => false]],
                    ],
                    30
                ), '会员增长趋势'));
                break;
        }

        return $page;
    }
}
