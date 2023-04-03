<?php


namespace Module\Member\Web\Controller;


use Illuminate\Support\Facades\View;
use ModStart\App\Web\Layout\WebConfigBuilder;
use ModStart\App\Web\Layout\WebPage;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Field\AbstractField;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Form\Form;
use ModStart\Grid\Grid;
use ModStart\Grid\GridFilter;
use ModStart\Module\ModuleManager;
use ModStart\Repository\Filter\RepositoryFilter;
use Module\Member\Auth\MemberUser;
use Module\Member\Support\MemberLoginCheck;

class MemberAddressController extends MemberFrameController implements MemberLoginCheck
{
    /** @var \Module\Member\Api\Controller\MemberAddressController */
    private $api;

    public function __construct()
    {
        parent::__construct();
        $this->api = app(\Module\Member\Api\Controller\MemberAddressController::class);
    }

    public function index(WebPage $page)
    {
        $grid = Grid::make('member_address');
        $grid->repositoryFilter(function (RepositoryFilter $filter) {
            $filter->where(['memberUserId' => MemberUser::id()]);
        });
        $grid->disableCUD()->disableItemOperate()
            ->canAdd(true)->urlAdd(action('\\' . __CLASS__ . '@add'))->addDialogSize(['60%', '80%']);
        $grid->useSimple(function (AbstractField $field, $item, $index) {
            return AutoRenderedFieldValue::makeView('module::Member.View.pc.memberAddress.item', [
                'item' => $item,
            ]);
        });
        $grid->title('地址');
        if (Request::isPost()) {
            return $grid->request();
        }
        list($view, $_) = $this->viewPaths('memberAddress.index');
        return $page->view($view)->pageTitle('我的地址')->body($grid);
    }

    private function doAddEdit()
    {
        $id = CRUDUtil::id();
        $record = null;
        if ($id) {
            $record = ModelUtil::get('member_address', ['id' => $id, 'memberUserId' => MemberUser::id()]);
            BizException::throwsIfEmpty('地址不存在', $record);
        }
        /** @var WebConfigBuilder $builder */
        $builder = app(WebConfigBuilder::class);
        $builder->useDialog();
        $builder->pageTitle(($id ? '修改' : '增加') . '地址');
        $builder->text('name', '姓名')->required();
        $builder->text('phone', '手机号')->required();
        if (modstart_module_enabled('Area')) {
            $builder->areaChina('area', '省市地区')->required();
        } else {
            $html = "<div class='tw-bg-gray-200 tw-rounded tw-px-2'>省市地区需要依赖 <a href='https://modstart.com/m/Area' target='_blank'>Area</a> 模块</div>";
            $builder->html('area', '省市地区')->html($html)->addable(true)->required();
        }

        $builder->textarea('detail', '详细地址')->required();
        $builder->text('post', '邮政编码');
        return $builder->perform(ArrayUtil::keepKeys($record, ['name', 'phone', 'area', 'detail', 'post']),
            function (Form $form) use ($id) {
                $data = $form->dataForming();
                if ($id) {
                    ModelUtil::update('member_address', $id, $data);
                } else {
                    $data['memberUserId'] = MemberUser::id();
                    ModelUtil::insert('member_address', $data);
                }
                return Response::redirect(CRUDUtil::jsDialogCloseAndParentGridRefresh());
            });
    }

    public function add()
    {
        return $this->doAddEdit();
    }

    public function edit()
    {
        return $this->doAddEdit();
    }

    public function delete()
    {
        $id = CRUDUtil::id();
        $record = ModelUtil::get('member_address', ['id' => $id, 'memberUserId' => MemberUser::id()]);
        BizException::throwsIfEmpty('地址不存在', $record);
        ModelUtil::delete('member_address', $id);
        return Response::redirect(CRUDUtil::jsGridRefresh());
    }

}
