<?php


namespace Module\Member\Web\Controller;

use ModStart\App\Web\Layout\WebPage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Field\AbstractField;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Grid\Displayer\ItemOperate;
use ModStart\Grid\Grid;
use ModStart\Grid\GridFilter;
use ModStart\ModStart;
use ModStart\Module\ModuleBaseController;
use ModStart\Repository\Filter\RepositoryFilter;
use ModStart\Widget\Box;
use ModStart\Widget\TextAction;
use Module\Member\Auth\MemberUser;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Type\MemberMessageStatus;

class MemberMessageController extends MemberFrameController implements MemberLoginCheck
{
    /** @var \Module\Member\Api\Controller\MemberMessageController */
    private $api;

    public function __construct()
    {
        parent::__construct();
        $this->api = app(\Module\Member\Api\Controller\MemberMessageController::class);
    }

    public function index(WebPage $page)
    {
        $grid = Grid::make('member_message');
        $grid->gridFilter(function (GridFilter $filter) {
            $filter->eq('status', '状态')->radio(MemberMessageStatus::class);
        });
        $grid->batchOperatePrepend('<button class="btn" data-batch-read-all><i class="iconfont icon-checked"></i> 全部已读</button>');
        ModStart::scriptFile('module/Member/Web/Controller/memberMessage.js');
        $grid->repositoryFilter(function (RepositoryFilter $filter) {
            $filter->where(['userId' => MemberUser::id()]);
        });
        $grid->disableCUD()->disableItemOperate();
        $grid->useSimple(function (AbstractField $field, $item, $index) {
            return AutoRenderedFieldValue::makeView('module::Member.View.pc.memberMessage.item', [
                'item' => $item,
            ]);
        });
        return $page->pageTitle('我的消息')->view($this->viewMemberFrame)->body(new Box($grid, '我的消息'))->handleGrid($grid);
    }

    public function delete()
    {
        return Response::sendFromGenerate($this->api->delete());
    }

    public function read()
    {
        return Response::sendFromGenerate($this->api->read());
    }

    public function readAll()
    {
        return Response::sendFromGenerate($this->api->readAll());
    }
}
