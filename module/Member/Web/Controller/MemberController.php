<?php


namespace Module\Member\Web\Controller;


use ModStart\Admin\Widget\DashboardItemA;
use ModStart\App\Web\Layout\WebPage;
use ModStart\Layout\Row;
use Module\Member\Config\MemberHomeIcon;
use Module\Member\Support\MemberLoginCheck;

class MemberController extends MemberFrameController implements MemberLoginCheck
{
    public function index(WebPage $page)
    {
        $page->view('module::Member.View.pc.member.index');
        foreach (MemberHomeIcon::get() as $group) {
            $page->append(new Row(function (Row $row) use ($group) {
                foreach ($group['children'] as $child) {
                    $row->column(['md' => 2, '' => 4], DashboardItemA::makeIconTitleLink($child['icon'], $child['title'], $child['url']));
                }
            }));
        }
        $page->pageTitle('我的');
        return $page;
    }
}
