<?php


namespace Module\Member\Web\Controller;

use ModStart\App\Web\Layout\WebPage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Field\AbstractField;
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

class MemberMessageController extends ModuleBaseController implements MemberLoginCheck
{
    
    private $api;

    public function __construct()
    {
        $this->api = app(\Module\Member\Api\Controller\MemberMessageController::class);
    }

    public function index(WebPage $page)
    {
        $grid = Grid::make('member_message', function (Grid $grid) {
            $grid->datetime('created_at', '时间');
            $grid->richHtml('content', '内容')->hookRendering(function (AbstractField $field, $item, $index) {
                return "<div style='word-break:break-all;'>{$item->content}</div>";
            })->listable(true);
            $grid->type('status', '状态')->type(MemberMessageStatus::class)->width(80);
            $grid->repositoryFilter(function (RepositoryFilter $filter) {
                $filter->where(['userId' => MemberUser::id()]);
            });
            $grid->gridFilter(function (GridFilter $filter) {
                $filter->eq('status', '状态')->radio(MemberMessageStatus::class);
            });
        });
        $grid->hookItemOperateRendering(function (ItemOperate $itemOperate) {
            $itemOperate->getField()->width(150);
            if ($itemOperate->item()->status === MemberMessageStatus::UNREAD) {
                $itemOperate->prepend(TextAction::primary('设为已读', 'data-item-read'));
            }
        });
        $grid->batchOperatePrepend(
            '<button class="btn" data-batch-item-read><i class="iconfont icon-checked"></i> 设为已读</button>
                    <button class="btn" data-batch-read-all><i class="iconfont icon-checked"></i> 全部已读</button>');
        $grid->disableCUD()
            ->canDelete(true)->canBatchDelete(true)->urlDelete(action('\\' . __CLASS__ . '@delete'));
        if (Request::isPost()) {
            return $grid->request();
        }
        ModStart::scriptFile(__DIR__ . '/memberMessage.js');
        list($view, $frame) = $this->viewPaths('member.frame');
        return $page->pageTitle('消息中心')->view($view)->body(new Box($grid, '消息中心'));
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
