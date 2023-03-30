<?php


namespace Module\Member\Web\Controller;


use ModStart\App\Web\Layout\WebPage;
use ModStart\Core\Input\Request;
use ModStart\Field\AbstractField;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Grid\Grid;
use ModStart\Grid\GridFilter;
use ModStart\Repository\Filter\RepositoryFilter;
use Module\Member\Auth\MemberUser;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Util\MemberMoneyUtil;

class MemberMoneyCashController extends MemberFrameController implements MemberLoginCheck
{
    /** @var \Module\Member\Api\Controller\MemberMoneyCashController */
    private $api;

    /**
     * MemberMoneyCashController constructor.
     * @param \Module\Member\Api\Controller\MemberMoneyCashController $api
     */
    public function __construct(\Module\Member\Api\Controller\MemberMoneyCashController $api)
    {
        parent::__construct();
        $this->api = $api;
    }

    public function index()
    {
        $total = MemberMoneyUtil::getTotal(MemberUser::id());
        return $this->view('memberMoneyCash.index', [
            'pageTitle' => '钱包提现',
            'total' => $total,
        ]);
    }

    public function log(WebPage $page)
    {
        $grid = Grid::make('member_money_cash');
        $grid->repositoryFilter(function (RepositoryFilter $filter) {
            $filter->where(['memberUserId' => MemberUser::id()]);
        });
        $grid->gridFilter(function (GridFilter $filter) {
            $filter->range('created_at', '时间')->datetime();
        });
        $grid->disableCUD()->disableItemOperate();
        $grid->useSimple(function (AbstractField $field, $item, $index) {
            return AutoRenderedFieldValue::makeView('module::Member.View.pc.memberMoneyCash.logItem', [
                'item' => $item,
            ]);
        });
        if (Request::isPost()) {
            return $grid->request();
        }
        list($view, $_) = $this->viewPaths('memberMoneyCash.log');
        return $page->pageTitle('钱包提现')->view($view)->body($grid);
    }

}
