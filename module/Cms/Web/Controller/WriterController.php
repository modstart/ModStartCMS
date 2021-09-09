<?php


namespace Module\Cms\Web\Controller;


use ModStart\App\Web\Layout\WebConfigBuilder;
use ModStart\App\Web\Layout\WebPage;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Field\Tags;
use ModStart\Form\Form;
use ModStart\Grid\Displayer\ItemOperate;
use ModStart\Grid\Grid;
use ModStart\Grid\GridFilter;
use ModStart\Repository\Filter\RepositoryFilter;
use ModStart\Widget\Box;
use ModStart\Widget\TextAjaxRequest;
use ModStart\Widget\TextLink;
use Module\Cms\Type\PostEditorType;
use Module\Member\Auth\MemberUser;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Web\Controller\MemberFrameController;

class WriterController extends MemberFrameController implements MemberLoginCheck
{
    
    private $api;

    public function __construct()
    {
        parent::__construct();
        $this->api = app(\Module\Cms\Api\Controller\WriterController::class);
    }

    public function index()
    {
        return Response::redirect('writer/post');
    }


    public function setting(WebConfigBuilder $builder)
    {
        $builder->radio('cmsEditorType', '编辑器类型')->optionType(PostEditorType::class);
        $builder->view($this->viewMemberFrame)->pageTitle('写作设置');
        return $builder->perform(Response::tryGetData($this->api->settingGet()), function () {
            return $this->api->settingSave();
        });
    }

    public function category(WebPage $page)
    {
        $grid = Grid::make('cms_member_post_category');
        $grid->text('title', '标题')->required();
        $grid->repositoryFilter(function (RepositoryFilter $filter) {
            $filter->where('memberUserId', MemberUser::id());;
        });
        $grid->gridFilter(function (GridFilter $filter) {
            $filter->eq('title', '标题');
        });
        $grid->title('分类');
        $grid->urlAdd(action('\\' . __CLASS__ . '@categoryEdit'));
        $grid->urlEdit(action('\\' . __CLASS__ . '@categoryEdit'));
        $grid->urlDelete(action('\\' . __CLASS__ . '@categoryDelete'));
        if (Request::isPost()) {
            return $grid->request();
        }
        $grid->dialogSizeSmall();
        return $page->view($this->viewMemberFrame)->pageTitle('分类管理')->body(new Box($grid, '分类管理'));
    }

    public function categoryEdit(WebConfigBuilder $builder)
    {
        $id = CRUDUtil::id();
        $record = null;
        if ($id) {
            $record = ModelUtil::get('cms_member_post_category', [
                'id' => $id, 'memberUserId' => MemberUser::id(),
            ]);
            BizException::throwsIfEmpty('记录不存在', $record);
        }
        $builder->text('title', '标题')->required();
        $builder->useDialog();
        $builder->page()->pageTitle($id ? '编辑分类' : '增加分类');
        return $builder->perform($record, function (Form $form) use ($id) {
            $data = $form->dataForming();
            if ($id) {
                ModelUtil::update('cms_member_post_category', $id, $data);
            } else {
                $data['memberUserId'] = MemberUser::id();
                ModelUtil::insert('cms_member_post_category', $data);
            }
            return Response::generateSuccess('保存成功');
        });
    }

    public function categoryDelete()
    {
        $id = CRUDUtil::id();
        $record = ModelUtil::get('cms_member_post_category', [
            'id' => $id, 'memberUserId' => MemberUser::id(),
        ]);
        BizException::throwsIfEmpty('记录不存在', $record);
        if (ModelUtil::exists('cms_member_post', ['categoryId' => $id])) {
            return Response::generateError('当前分类有文章，不能删除');
        }
        ModelUtil::delete('cms_member_post_category', $id);
        return Response::generateSuccess();
    }

    public function post(WebPage $page)
    {
        $grid = Grid::make('cms_member_post');
        $grid->select('categoryId', '分类')
            ->optionModel('cms_member_post_category', 'id', 'title', ['memberUserId' => MemberUser::id()])
            ->width(100);
        $grid->text('title', '标题')->width(200);
        $grid->switch('isPublished', '已发布')->optionsYesNo();
        $grid->switch('isOriginal', '原创')->optionsYesNo();
        $grid->tags('tags', '标签')->serializeType(Tags::SERIALIZE_TYPE_COLON_SEPARATED);
        $grid->datetime('created_at', '创建');
        $grid->datetime('updated_at', '更新');
        $grid->repositoryFilter(function (RepositoryFilter $filter) {
            $filter->where('memberUserId', MemberUser::id());
        });
        $grid->gridFilter(function (GridFilter $filter) {
            $filter->eq('categoryId', '分类')->selectModel('cms_member_post_category', 'id', 'title', ['memberUserId' => MemberUser::id()]);
            $filter->eq('title', '标题');
            $filter->eq('isPublished', '已发布')->switchRadioYesNo();
        });
        $grid->urlAdd(action('\\' . __CLASS__ . '@postEdit'))->addBlankPage(true);
        $grid->urlEdit(action('\\' . __CLASS__ . '@postEdit'))->editBlankPage(true);
        $grid->urlDelete(modstart_api_url('writer/post/delete'));
        $grid->title('文章');
        $grid->hookItemOperateRendering(function (ItemOperate $itemOperate) {
            $item = $itemOperate->item();
            if ($item->isPublished) {
                $post = ModelUtil::get('cms_post', ['memberPostId' => $item->id]);
                if (!empty($post)) {
                    $itemOperate->prepend(TextLink::primary('查看发布', modstart_web_url('p/' . $post['alias']), 'target="_blank"'));
                    $itemOperate->prepend(TextAjaxRequest::primary('更新发布', modstart_api_url('writer/post/publish?id=' . $item->id), '确定更新发布？'));
                }
                $itemOperate->prepend(TextAjaxRequest::danger('取消发布', modstart_api_url('writer/post/publish_cancel?id=' . $item->id), '确定取消发布？'));
            } else {
                $itemOperate->prepend(TextAjaxRequest::primary('立即发布', modstart_api_url('writer/post/publish?id=' . $item->id), '确定发布？'));
            }
            $itemOperate->getField()->width(300);
        });
        if (Request::isPost()) {
            return $grid->request();
        }
        return $page->view($this->viewMemberFrame)->pageTitle('文章管理')->body(new Box($grid, '文章管理'));
    }

    public function postEdit()
    {
        return $this->view('writer.edit');
    }
}
