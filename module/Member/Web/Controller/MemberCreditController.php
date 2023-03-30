<?php


namespace Module\Member\Web\Controller;


use Illuminate\Support\Facades\View;
use ModStart\App\Web\Layout\WebPage;
use ModStart\Core\Input\Request;
use ModStart\Field\AbstractField;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Grid\Grid;
use ModStart\Grid\GridFilter;
use ModStart\Module\ModuleBaseController;
use ModStart\Module\ModuleManager;
use ModStart\Repository\Filter\RepositoryFilter;
use Module\Member\Auth\MemberUser;
use Module\Member\Support\MemberLoginCheck;

class MemberCreditController extends ModuleBaseController implements MemberLoginCheck
{
    /** @var \Module\Member\Api\Controller\MemberCreditController */
    private $api;

    public function __construct()
    {
        list($this->viewMemberFrame, $_) = $this->viewPaths('member.frame');
        View::share('_viewMemberFrame', $this->viewMemberFrame);
        $this->api = app(\Module\Member\Api\Controller\MemberCreditController::class);
    }

    public function index(WebPage $page)
    {
        $grid = Grid::make('member_credit_log');
        $grid->repositoryFilter(function (RepositoryFilter $filter) {
            $filter->where(['memberUserId' => MemberUser::id()]);
        });
        $grid->gridFilter(function (GridFilter $filter) {
            $filter->range('created_at', '时间')->datetime();
        });
        $grid->disableCUD()->disableItemOperate();
        $grid->useSimple(function (AbstractField $field, $item, $index) {
            return AutoRenderedFieldValue::makeView('module::Member.View.pc.memberCredit.item', [
                'item' => $item,
            ]);
        });
        if (Request::isPost()) {
            return $grid->request();
        }
        list($view, $_) = $this->viewPaths('memberCredit.index');
        $page->pageTitle('我的' . ModuleManager::getModuleConfig('Member', 'creditName', '积分'));
        return $page->view($view)->body($grid);
    }

}
