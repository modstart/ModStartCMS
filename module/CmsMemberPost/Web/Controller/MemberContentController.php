<?php


namespace Module\CmsMemberPost\Web\Controller;


use Carbon\Carbon;
use ModStart\App\Web\Layout\WebPage;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Field\AbstractField;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Grid\Grid;
use ModStart\Grid\GridFilter;
use ModStart\ModStart;
use ModStart\Repository\Filter\RepositoryFilter;
use ModStart\Widget\Box;
use Module\Cms\Field\CmsField;
use Module\Cms\Type\CmsContentVerifyStatus;
use Module\Cms\Type\CmsModelContentStatus;
use Module\Cms\Util\CmsCatUtil;
use Module\Cms\Util\UrlUtil;
use Module\Member\Auth\MemberUser;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Web\Controller\MemberFrameController;

class MemberContentController extends MemberFrameController implements MemberLoginCheck
{
    public function index(WebPage $page)
    {
        $grid = Grid::make('cms_content');
        $grid->repositoryFilter(function (RepositoryFilter $filter) {
            $filter->where(['memberUserId' => MemberUser::id()]);
        });
        $grid->useSimple(function (AbstractField $field, $item, $index) {
            list($useView, $_) = $this->viewPaths('cmsMemberPost.memberContent.item');
            $item->_url = UrlUtil::content($item->toArray());
            return AutoRenderedFieldValue::makeView($useView, [
                'item' => $item,
            ]);
        });
        $grid->gridFilter(function (GridFilter $filter) {
            $filter->like('title', '名称');
        });
        $grid->disableCUD();
        if (Request::isPost()) {
            return $grid->request();
        }
        return $page->pageTitle('我的内容')->view($this->viewMemberFrame)->body(Box::make($grid, '我的内容'));
    }


    public function edit()
    {
        $input = InputPackage::buildFromInput();
        $catId = $input->getInteger('catId');
        $id = $input->getInteger('id');
        $cat = null;
        $record = null;
        $recordData = null;
        $model = null;
        $modelTable = 'cms_content';
        $modelDataTable = null;
        if (!empty($id)) {
            $record = ModelUtil::get($modelTable, ['id' => $id, 'memberUserId' => MemberUser::id()]);
            BizException::throwsIfEmpty('记录不存在', $record);
            $cat = CmsCatUtil::get($record['catId']);
            $model = $cat['_model'];
            $modelDataTable = "cms_m_" . $model['name'];
            $recordData = ModelUtil::get($modelDataTable, ['id' => $record['id']]);
        }
        if (empty($cat) && !empty($catId)) {
            $cat = CmsCatUtil::get($catId);
        }
        if (empty($cat)) {
            $catTreeCanPost = CmsCatUtil::treeWithPost(function ($cat) {
                return \MCms::canPostCat($cat);
            });
            return $this->view('cmsMemberPost.memberContent.catSelect', [
                'catTreeCanPost' => $catTreeCanPost,
            ]);
        }
        $model = $cat['_model'];
        $modelDataTable = "cms_m_" . $model['name'];
        BizException::throwsIf('栏目不能发布', !\MCms::canPostCat($cat));
        if (Request::isPost()) {
            $input = InputPackage::buildFromInput();
            $submitData = $input->all();
            $recordValue = [];
            $recordValue['catId'] = $cat['id'];
            $recordValue['title'] = $input->getTrimString('title');
            $recordValue['cover'] = $input->getImagePath('cover');
            $recordValue['tags'] = $input->getTrimString('tags');
            BizException::throwsIfEmpty('标题不能为空', $recordValue['title']);
            BizException::throwsIfEmpty('缩略图不能为空', $recordValue['cover']);
            $recordDataValue = [];
            $dataKeys = [];
            foreach ($model['_customFields'] as $field) {
                $dataKeys[] = $field['name'];
                $f = CmsField::getByNameOrFail($field['fieldType']);
                $recordDataValue[$field['name']] = $f->prepareInputOrFail($field, $input);
                if ($field['isRequired']) {
                    if (empty($recordDataValue[$field['name']])) {
                        return Response::generateError($field['title'] . '为空');
                    }
                }
                $recordDataValue[$field['name']] = $f->serializeValue($recordDataValue[$field['name']], $submitData);
            }
            $recordDataValue['content'] = $input->getRichContent('content');
            ModelUtil::transactionBegin();
            if (!empty($record)) {
                if (ArrayUtil::isChanged($recordValue, $record, ['title', 'cover'])) {
                    $recordValue['verifyStatus'] = CmsContentVerifyStatus::VERIFYING;
                }
                if (ArrayUtil::isChanged($recordDataValue, $recordData, $dataKeys)) {
                    $recordValue['verifyStatus'] = CmsContentVerifyStatus::VERIFYING;
                }
                ModelUtil::update($modelTable, $record['id'], $recordValue);
                ModelUtil::update($modelDataTable, $record['id'], $recordDataValue);
            } else {
                $recordValue['memberUserId'] = MemberUser::id();
                $recordValue['modelId'] = $model['id'];
                $recordValue['status'] = CmsModelContentStatus::SHOW;
                $recordValue['verifyStatus'] = CmsContentVerifyStatus::VERIFYING;
                $recordValue['postTime'] = Carbon::now();
                $recordValue['isTop'] = false;
                $recordValue = ModelUtil::insert($modelTable, $recordValue);
                $recordDataValue['id'] = $recordValue['id'];
                ModelUtil::insert($modelDataTable, $recordDataValue);
            }
            ModelUtil::transactionCommit();
            return Response::redirect(modstart_web_url('cms_member_content'));
        }
        ModStart::script('window.__selectorDialogServer = "' . modstart_web_url('member_data/file_manager') . '";');
        $viewData = [
            'cat' => $cat,
            'model' => $model,
            'record' => $record,
            'recordData' => $recordData,
        ];
        return $this->view('cmsMemberPost.memberContent.edit', $viewData);
    }
}
