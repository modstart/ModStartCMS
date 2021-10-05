<?php


namespace Module\Member\Web\Controller;


use ModStart\App\Web\Layout\WebPage;
use ModStart\Layout\Row;
use ModStart\Widget\Box;

class MemberController extends MemberFrameController
{
    public function index(WebPage $page)
    {
        $page->view($this->viewMemberFrame);
        $page->row(function (Row $row) {
            $row->column(12, Box::make('test', 'test'));
        });
        return $page;
    }
}
