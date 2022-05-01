<?php


namespace Module\Vendor\QuickRun\CustomField;


use Illuminate\Routing\Controller;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Form\Form;
use ModStart\Grid\Grid;

abstract class AbstractCustomFieldManageController extends Controller
{
    abstract protected function config();

    public function index(AdminPage $page)
    {
        $config = $this->config();
        $grid = Grid::make($config['tableField']);
        $grid->text('title', '名称');
        $grid->text('name', '标识');
        $grid->switch('enable', '启用')->optionsYesNo();
        $grid->canSort(true)->defaultOrder(['sort', 'asc']);
        $grid->canAdd(true)->urlAdd(action($config['class'] . '@edit'));
        $grid->canEdit(true)->urlEdit(action($config['class'] . '@edit'));
        $grid->canDelete(true)->urlDelete(action($config['class'] . '@delete'));
        $grid->canSort(true)->urlSort(action($config['class'] . '@sort'));
        return $page->pageTitle($config['title'])->body($grid)->handleGrid($grid);
    }

    public function add()
    {
        return $this->edit();
    }

    public function edit()
    {
        $config = $this->config();
        $id = CRUDUtil::id();
        $record = [
            'title' => '',
            'name' => '',
            'enable' => true,
            'fieldType' => 'text',
            'fieldData' => new \stdClass(),
            'isRequired' => true,
            'isSearch' => true,
            'isList' => true,
            'placeholder' => '',
            'maxLength' => 100,
        ];
        if ($id) {
            $record = ModelUtil::get($config['tableField'], $id);
            BizException::throwsIfEmpty('记录不存在', $record);
            ModelUtil::decodeRecordJson($record, ['fieldData']);
            ModelUtil::decodeRecordBoolean($record, ['enable', 'isRequired', 'isSearch', 'isList']);
        }
        if (Request::isPost()) {
            AdminPermission::demoCheck();
            $input = InputPackage::buildFromInputJson('data');
            $data = [];
            $data['title'] = $input->getTrimString('title');
            $data['name'] = $input->getTrimString('name');
            $data['enable'] = $input->getBoolean('enable');
            $data['fieldType'] = $input->getType('fieldType', CustomFieldType::class);
            $data['fieldData'] = $input->getArray('fieldData');
            $data['isRequired'] = $input->getBoolean('isRequired');
            $data['isSearch'] = $input->getBoolean('isSearch');
            $data['isList'] = $input->getBoolean('isList');
            $data['placeholder'] = $input->getTrimString('placeholder');
            $data['maxLength'] = $input->getInteger('maxLength');
            BizException::throwsIfEmpty('标题为空', $data['title']);
            BizException::throwsIfEmpty('标识为空', $data['name']);
            if ($config['tableFieldPrefix']) {
                $data['name'] = $config['tableFieldPrefix'] . $data['name'];
            }
            BizException::throwsIf('标识格式不正确', !preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $data['name']));
            BizException::throwsIf('标识不能为系统关键字段', in_array($data['name'], [
                'id', 'created_at', 'updated_at',
            ]));
            $unique = ModelUtil::isFieldUniqueForInsertOrUpdate($config['tableField'], $id, 'name', $data['name']);
            BizException::throwsIf('标识重复', !$unique);
            BizException::throwsIf('标识长度范围1-50', strlen($data['name']) < 1 || strlen($data['name']) > 50);
            BizException::throwsIfEmpty('字段类型为空', $data['fieldType']);
            switch ($data['fieldType']) {
                case CustomFieldType::RADIO:
                case CustomFieldType::SELECT:
                case CustomFieldType::CHECKBOX:
                    BizException::throwsIf('选项为空', empty($data['fieldData']['options']));
                    $data['fieldData']['options'] = array_filter(array_map(function ($v) {
                        return trim($v);
                    }, $data['fieldData']['options']));
                    BizException::throwsIf('选项为空', empty($data['fieldData']['options']));
                    break;
            }
            switch ($data['fieldType']) {
                case CustomFieldType::TEXT:
                case CustomFieldType::TEXTAREA:
                case CustomFieldType::RADIO:
                case CustomFieldType::SELECT:
                case CustomFieldType::CHECKBOX:
                    BizException::throwsIf('字段长度错误', $data['maxLength'] < 1 || $data['maxLength'] > 65535);
                    break;
                case CustomFieldType::IMAGE:
                case CustomFieldType::FILE:
                    $data['maxLength'] = 200;
                    break;
                case CustomFieldType::IMAGES:
                    $data['maxLength'] = 1000;
                    break;
            }
            $data['fieldData'] = json_encode($data['fieldData']);
            ModelUtil::transactionBegin();
            if ($id) {
                ModelUtil::update($config['tableField'], $id, $data);
                CustomFieldUtil::editField($config['tableData'], $record, $data);
            } else {
                $data['sort'] = ModelUtil::sortNext($config['tableField']);
                $data = ModelUtil::insert($config['tableField'], $data);
                CustomFieldUtil::addField($config['tableData'], $data);
            }
            ModelUtil::transactionCommit();
            CustomFieldUtil::clearCache($config['tableField']);
            return Response::generateSuccess();
        }
        if ($record['name'] && $config['tableFieldPrefix']) {
            $record['name'] = substr($record['name'], strlen($config['tableFieldPrefix']));
        }
        return view('module::Vendor.View.quickRun.customField.edit', [
            'fieldNamePrefix' => $config['tableFieldPrefix'],
            'record' => $record,
        ]);
    }


    public function delete()
    {
        $config = $this->config();
        AdminPermission::demoCheck();
        $id = CRUDUtil::id();
        $record = ModelUtil::get($config['tableField'], $id);
        BizException::throwsIfEmpty('记录不存在', $record);
        ModelUtil::transactionBegin();
        CustomFieldUtil::deleteField($config['tableData'], $record);
        ModelUtil::delete($config['tableField'], $id);
        ModelUtil::transactionCommit();
        CustomFieldUtil::clearCache($config['tableField']);
        return Response::generateSuccess();
    }

    public function sort()
    {
        AdminPermission::demoCheck();
        $config = $this->config();
        $form = Form::make($config['tableField']);
        $form->canSort(true);
        return $form->sortRequest(CRUDUtil::id());
    }

}
