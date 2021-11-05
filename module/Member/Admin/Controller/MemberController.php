<?php


namespace Module\Member\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Admin\Layout\AdminDialogPage;
use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Core\Util\RandomUtil;
use ModStart\Field\AbstractField;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Form\Form;
use ModStart\Form\Type\FormMode;
use ModStart\Grid\GridFilter;
use ModStart\Module\ModuleManager;
use ModStart\Support\Concern\HasFields;
use Module\Member\Type\MemberStatus;
use Module\Member\Util\MemberGroupUtil;
use Module\Member\Util\MemberUtil;

class MemberController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init('member_user')
            ->field(function ($builder) {
                
                $builder->id('id', 'ID');
                $builder->display('avatar', '头像')->hookRendering(function (AbstractField $field, $item, $index) {
                    $avatarSmall = AssetsUtil::fixOrDefault($item->avatar, 'asset/image/avatar.png');
                    $avatarBig = AssetsUtil::fixOrDefault($item->avatarBig, 'asset/image/avatar.png');
                    return AutoRenderedFieldValue::make("<a href='$avatarBig' class='tw-inline-block' data-image-preview>
                        <img src='$avatarSmall' class='tw-rounded-full tw-w-8 tw-h-8 tw-shadow'></a>");
                });
                $builder->text('username', '用户名')->required()->ruleUnique('member_user');
                $builder->text('password', '初始密码')
                    ->editable(false)->addable(true)
                    ->listable(false)->showable(false)->required()->defaultValue(RandomUtil::lowerString(8));
                $builder->text('email', '邮箱');
                $builder->text('phone', '手机');
                $builder->type('status', '状态')->type(MemberStatus::class, [
                    MemberStatus::NORMAL => 'success',
                    MemberStatus::FORBIDDEN => 'danger',
                ])->required();
                if (ModuleManager::getModuleConfigBoolean('Member', 'groupEnable', false)) {
                    $builder->radio('groupId', '分组')->options(MemberGroupUtil::mapIdTitle())->required();
                }
                $builder->display('created_at', '创建时间');
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('id', L('ID'));
                $filter->eq('status', '状态')->select(MemberStatus::class);
                if (ModuleManager::getModuleConfigBoolean('Member', 'groupEnable', false)) {
                    $filter->eq('groupId', '分组')->select(MemberGroupUtil::mapIdTitle());
                }
                $filter->like('username', '用户名');
                $filter->like('email', '邮箱');
                $filter->like('phone', '手机');
            })
            ->hookSaved(function (Form $form) {
                
                $item = $form->item();
                switch ($form->mode()) {
                    case FormMode::ADD:
                        MemberUtil::changePassword($item->id, $item->password, null, true);
                        break;
                }
            })
            ->dialogSizeSmall()
            ->title('用户管理')
            ->canDelete(false);
    }

    public function select(AdminDialogPage $page)
    {
        $grid = $this->grid();
        $grid->disableCUD();
        $grid->canSingleSelectItem(true);
        CRUDUtil::registerGridResource($grid, '\\' . __CLASS__);
        if (Request::isPost()) {
            return $grid->request();
        }
        return $page->pageTitle('选择用户')->body($grid);
    }

    public function search()
    {
        $input = InputPackage::buildFromInput();
        $keywords = $input->getTrimString('keywords');
        $option = [];
        $option['whereOperate'] = ['username', 'like', "%$keywords%"];
        $paginateData = MemberUtil::paginate(1, 10, $option);
        $records = array_map(function ($item) {
            return [
                'value' => intval($item['id']),
                'name' => htmlspecialchars(MemberUtil::viewName($item)),
                'avatar' => AssetsUtil::fixOrDefault($item['avatar'], 'asset/image/avatar.png'),
            ];
        }, $paginateData['records']);
        return Response::jsonSuccessData($records);
    }

}
