<?php


namespace Module\Cms\Web\Controller;


use Carbon\Carbon;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use Module\Cms\Type\CmsModelFieldType;
use Module\Cms\Util\CmsContentUtil;
use Module\Cms\Util\CmsTemplateUtil;

class FormController extends BaseCatController
{
    public function index($id = 0)
    {
        $data = parent::setup($id);
        $view = $this->getView($data, 'formTemplate');
        $viewData = $data;
        return $this->view('cms.form.' . CmsTemplateUtil::toBladeView($view), $viewData);
    }

    public function submit($id = 0)
    {
        $data = parent::setup($id);
        $input = InputPackage::buildFromInput();
        $submitData = [];
        $submitData['content'] = $input->getRichContent('content');
        $customFields = isset($data['cat']['_model']['_customFields']) ? $data['cat']['_model']['_customFields'] : [];
        if (!empty($customFields)) {
            foreach ($customFields as $customField) {
                switch ($customField['fieldType']) {
                    case CmsModelFieldType::TEXT:
                    case CmsModelFieldType::TEXTAREA:
                    case CmsModelFieldType::RADIO:
                    case CmsModelFieldType::SELECT:
                    case CmsModelFieldType::RICH_TEXT:
                        $submitData[$customField['name']] = $input->getTrimString($customField['name']);
                        break;
                    case CmsModelFieldType::CHECKBOX:
                        $submitData[$customField['name']] = $input->getArray($customField['name']);
                        break;
                    case CmsModelFieldType::IMAGE:
                        $submitData[$customField['name']] = $input->getImagePath($customField['name']);
                        break;
                    case CmsModelFieldType::FILE:
                        $submitData[$customField['name']] = $input->getFilePath($customField['name']);
                        break;
                    case CmsModelFieldType::DATE:
                        $submitData[$customField['name']] = $input->getDate($customField['name']);
                        break;
                    case CmsModelFieldType::DATETIME:
                        $submitData[$customField['name']] = $input->getDatetime($customField['name']);
                        break;
                    default:
                        return Response::generateError('错误的字段类型');
                }
                if (!empty($customField['isRequired'])) {
                    if (empty($submitData[$customField['name']])) {
                        return Response::generateError($customField['title'] . '不能为空');
                    }
                }
                switch ($customField['fieldType']) {
                    case CmsModelFieldType::CHECKBOX:
                        $submitData[$customField['name']] = json_encode($submitData[$customField['name']], JSON_UNESCAPED_UNICODE);
                        break;
                }
            }
        }
        if (empty($submitData['content'])) {
            if (Request::isAjax()) {
                return Response::generateError('内容为空');
            }
            return Response::send(-1, '内容为空', null, Request::headerReferer());
        }
        $submitDataPrimary = [];
        $submitDataPrimary['catId'] = $data['cat']['id'];
        $submitDataPrimary['postTime'] = Carbon::now();
        CmsContentUtil::insert($data['cat']['_model'], $submitDataPrimary, $submitData);
        if (Request::isAjax()) {
            return Response::generate(0, '提交成功', null, '[reload]');
        }
        return Response::send(0, '提交成功', null, Request::headerReferer());
    }
}