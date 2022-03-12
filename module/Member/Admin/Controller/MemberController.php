<?php


namespace Module\Member\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminConfigBuilder;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Admin\Layout\AdminDialogPage;
use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Exception\BizException;
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
use Module\Member\Config\MemberAdminList;
use Module\Member\Provider\MemberAdminShowPanel\MemberAdminShowPanelProvider;
use Module\Member\Type\MemberStatus;
use Module\Member\Util\MemberGroupUtil;
use Module\Member\Util\MemberMessageUtil;
use Module\Member\Util\MemberUtil;
use Module\Member\Util\MemberVipUtil;

class MemberController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init('member_user')
            ->field(function ($builder) {
                /** @var HasFields $builder */
                $builder->id('id', 'ID');
                MemberAdminList::callGridField($builder);
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
                if (ModuleManager::getModuleConfigBoolean('Member', 'vipEnable', false)) {
                    $builder->radio('vipId', 'VIP')->options(MemberVipUtil::mapTitle())->required();
                    $builder->date('vipExpire', 'VIP过期');
                }
                $builder->display('created_at', '注册时间');
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('id', L('ID'));
                $filter->like('username', '用户名');
                $filter->like('email', '邮箱')->autoHide(true);
                $filter->like('phone', '手机')->autoHide(true);
                $filter->eq('status', '状态')->autoHide(true)->select(MemberStatus::class);
                if (ModuleManager::getModuleConfigBoolean('Member', 'groupEnable', false)) {
                    $filter->eq('groupId', '分组')->autoHide(true)->select(MemberGroupUtil::mapIdTitle());
                }
                if (ModuleManager::getModuleConfigBoolean('Member', 'vipEnable', false)) {
                    $filter->eq('vipId', 'VIP')->autoHide(true)->select(MemberVipUtil::mapTitle());
                }
            })
            ->hookSaved(function (Form $form) {
                /** @var \stdClass $item */
                $item = $form->item();
                switch ($form->mode()) {
                    case FormMode::ADD:
                        MemberUtil::changePassword($item->id, $item->password, null, true);
                        break;
                }
            })
            ->title('用户')
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

    public function resetPassword(AdminConfigBuilder $builder)
    {
        $id = CRUDUtil::id();
        $memberUser = MemberUtil::get($id);
        BizException::throwsIfEmpty('用户不存在', $memberUser);
        $builder->useDialog();
        $builder->pageTitle('重置密码');
        $builder->text('passwordNew', '新密码')->required()->defaultValue(RandomUtil::upperString(6));
        if (Request::isPost()) {
            return $builder->formRequest(function (Form $form) use ($memberUser) {
                AdminPermission::demoCheck();
                $data = $form->dataForming();
                $ret = MemberUtil::changePassword($memberUser['id'], $data['passwordNew'], null, true);
                BizException::throwsIfResponseError($ret);
                return Response::redirect(CRUDUtil::jsDialogClose());
            });
        }
        return $builder;
    }

    public function sendMessage(AdminConfigBuilder $builder)
    {
        $id = CRUDUtil::id();
        $memberUser = MemberUtil::get($id);
        BizException::throwsIfEmpty('用户不存在', $memberUser);
        $builder->useDialog();
        $builder->pageTitle('发送消息');
        $builder->richHtml('content', '消息内容')->required();
        if (Request::isPost()) {
            return $builder->formRequest(function (Form $form) use ($memberUser) {
                AdminPermission::demoCheck();
                $data = $form->dataForming();
                $ret = MemberMessageUtil::send($memberUser['id'], $data['content']);
                BizException::throwsIfResponseError($ret);
                return Response::redirect(CRUDUtil::jsDialogClose());
            });
        }
        return $builder;
    }

    public function show()
    {
        $record = MemberUtil::get(CRUDUtil::id());
        $showPanelProviders = MemberAdminShowPanelProvider::listAll();
        return view('module::Member.View.admin.memberUser.show', [
            'record' => $record,
            'showPanelProviders' => $showPanelProviders,
        ]);
    }
}
