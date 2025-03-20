<?php


namespace Module\Member\Web\Controller;


use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use ModStart\Admin\Widget\DashboardItemA;
use ModStart\App\Web\Layout\WebPage;
use ModStart\Core\Input\Response;
use ModStart\Layout\Row;
use ModStart\Widget\Box;
use Module\Member\Config\MemberHomeIcon;
use Module\Member\Config\MemberHomePanel;
use Module\Member\Support\MemberLoginCheck;

class MemberController extends MemberFrameController implements MemberLoginCheck
{
    /** @var \Module\Member\Api\Controller\MemberController */
    private $api;

    /**
     * MemberController constructor.
     * @param \Module\Member\Api\Controller\MemberController $api
     */
    public function __construct(\Module\Member\Api\Controller\MemberController $api)
    {
        parent::__construct();
        $this->api = $api;
    }


    public function index(WebPage $page)
    {
        $viewBase = 'member.index';
        if (Input::get('dialog', 0)) {
            $viewBase = 'member.dialog';
            $this->shareDialogPageViewFrame();
        }
        list($view, $viewFrame) = $this->viewPaths($viewBase);
        $page->view($view);
        foreach (MemberHomePanel::get() as $panel) {
            $page->append(new Row(function (Row $row) use ($panel) {
                call_user_func_array($panel, [
                    $row
                ]);
            }));
        }
        foreach (MemberHomeIcon::get() as $group) {
            $page->append(Box::make(new Row(function (Row $row) use ($group) {
                foreach ($group['children'] as $child) {
                    $value = isset($child['value']) ? $child['value'] : null;
                    $row->column(['md' => 2, '' => 3], DashboardItemA::makeIconTitleValueLink($child['icon'], $child['title'], $value, $child['url']));
                }
            }), $group['title']));
        }
        $viewData = Response::tryGetData($this->api->current());
        View::share($viewData);
        $page->pageTitle('我的');
        return $page;
    }
}
