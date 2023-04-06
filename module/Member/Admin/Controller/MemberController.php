<?php


namespace Module\Member\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminConfigBuilder;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Admin\Layout\AdminDialogPage;
use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Type\TypeUtil;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Core\Util\ColorUtil;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Core\Util\EventUtil;
use ModStart\Core\Util\RandomUtil;
use ModStart\Core\Util\TimeUtil;
use ModStart\Field\AbstractField;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Field\Select;
use ModStart\Field\Type\FieldRenderMode;
use ModStart\Form\Form;
use ModStart\Grid\Displayer\ItemOperate;
use ModStart\Grid\GridFilter;
use ModStart\Module\ModuleManager;
use ModStart\Repository\Filter\RepositoryFilter;
use ModStart\Support\Concern\HasFields;
use ModStart\Widget\TextDialogRequest;
use Module\Member\Config\MemberAdminList;
use Module\Member\Config\MemberOauth;
use Module\Member\Events\MemberUserRegisteredEvent;
use Module\Member\Provider\MemberAdminShowPanel\MemberAdminShowPanelProvider;
use Module\Member\Type\Gender;
use Module\Member\Type\MemberStatus;
use Module\Member\Util\MemberGroupUtil;
use Module\Member\Util\MemberMessageUtil;
use Module\Member\Util\MemberUtil;
use Module\Member\Util\MemberVipUtil;
use Module\Vendor\QuickRun\Export\ExportHandle;

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
                    $avatarSmall = AssetsUtil::fixOrDefault($item->avatar, 'asset/image/avatar.svg');
                    $avatarBig = AssetsUtil::fixOrDefault($item->avatarBig, 'asset/image/avatar.svg');
                    return AutoRenderedFieldValue::make("<a href='$avatarBig' class='tw-inline-block' data-image-preview>
                        <img src='$avatarSmall' class='tw-rounded-full tw-w-8 tw-h-8 tw-shadow'></a>");
                });
                $builder->text('username', '用户名')->required()->ruleUnique('member_user')
                    ->hookRendering(function (AbstractField $field, $item, $index) {
                        switch ($field->renderMode()) {
                            case FieldRenderMode::GRID:
                            case FieldRenderMode::DETAIL:
                                return AutoRenderedFieldValue::make(
                                    TextDialogRequest::make(
                                        'primary',
                                        htmlspecialchars($item->username),
                                        modstart_admin_url('member/show', ['_id' => $item->id])
                                    )->width('90%')->height('90%')->render()
                                );
                                break;
                        }
                    });
                $builder->text('email', '邮箱');
                $builder->text('phone', '手机');
                $builder->text('nickname', '昵称');
                if (MemberOauth::hasEnableItems()) {
                    $builder->display('_oauth', '授权')->hookRendering(function (AbstractField $field, $item, $index) {
                        $oauthList = [];
                        $oauthRecords = MemberUtil::listOauths($item->id);
                        foreach ($oauthRecords as $oauthRecord) {
                            $color = null;
                            $title = $oauthRecord['type'];
                            $oauth = MemberOauth::getByOauthKey($oauthRecord['type']);
                            if ($oauth) {
                                $color = $oauth->color();
                                $title = $oauth->title();
                            }
                            if (empty($color)) {
                                $color = ColorUtil::pick($oauthRecord['type']);
                            }
                            $oauthList[] = '<a style="color:' . $color . ';" href="javascript:;" data-tip-popover="' . $title . '"><i class="iconfont icon-dot"></i></a>';
                        }
                        return join('', $oauthList);
                    });
                }
                $builder->type('status', '状态')->type(MemberStatus::class, [
                    MemberStatus::NORMAL => 'success',
                    MemberStatus::FORBIDDEN => 'danger',
                ])->required();
                // ->gridEditable(true)
                $groupEnable = ModuleManager::getModuleConfig('Member', 'groupEnable', false);
                if ($groupEnable) {
                    $builder->radio('groupId', '分组')->options(MemberGroupUtil::mapIdTitle())->required();
                }
                $vipEnable = ModuleManager::getModuleConfig('Member', 'vipEnable', false);
                if ($vipEnable) {
                    $builder->radio('vipId', 'VIP')->options(MemberVipUtil::mapTitle())->required();
                    $builder->date('vipExpire', 'VIP过期');
                }
                $builder->display('registerIp', '注册IP');
                $builder->display('created_at', '注册时间');
                $builder->canBatchSelect(true);
                $builder->batchOperatePrepend('<button class="btn" data-batch-confirm="确认禁用 %d 个用户？" data-batch-operate="' . modstart_admin_url('member/status_forbidden') . '"><i class="iconfont icon-warning"></i> 禁用</button>');
            })
            ->repositoryFilter(function (RepositoryFilter $filter) {
                $filter->where(['isDeleted' => false]);
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('id', L('ID'));
                $filter->like('username', '用户名');
                $filter->like('email', '邮箱')->autoHide(true);
                $filter->like('phone', '手机')->autoHide(true);
                $filter->eq('status', '状态')->autoHide(true)->select(MemberStatus::class);
                if (ModuleManager::getModuleConfig('Member', 'groupEnable', false)) {
                    $filter->eq('groupId', '分组')->autoHide(true)->select(MemberGroupUtil::mapIdTitle());
                }
                if (ModuleManager::getModuleConfig('Member', 'vipEnable', false)) {
                    $filter->eq('vipId', 'VIP')->autoHide(true)->select(MemberVipUtil::mapTitle());
                }
            })
            ->operateFixed('right')
            ->hookItemOperateRendering(function (ItemOperate $itemOperate) {
                $item = $itemOperate->item();
                $itemOperate->prepend(
                    TextDialogRequest::make(
                        'primary',
                        '查看',
                        modstart_admin_url('member/show', ['_id' => $item->id])
                    )->width('90%')->height('90%')->render()
                );
            })
            ->title('用户管理')
            ->canShow(false)
            ->canDelete(true)
            ->canEdit(false)
            ->canExport(ModuleManager::getModuleConfig('Member', 'exportEnable',false));
    }

    public function selectRemote()
    {
        return Select::optionRemoteHandleModel('member_user', 'id', 'username');
    }

    public function add(AdminDialogPage $page)
    {
        $form = Form::make('');
        $form->layoutPanel('基础（用户、手机、邮箱不能同时为空）', function (Form $form) {
            $form->text('username', '用户名');
            $form->text('phone', '手机');
            $form->text('email', '邮箱');
            $form->text('password', '初始密码')->defaultValue(RandomUtil::lowerString(8));
        });
        $form->layoutPanel('高级', function (Form $form) {
            $form->text('nickname', '昵称');
            $form->radio('status', '状态')->optionType(MemberStatus::class)->defaultValue(MemberStatus::NORMAL);
            if (ModuleManager::getModuleConfig('Member', 'groupEnable', false)) {
                $form->radio('groupId', '分组')->options(MemberGroupUtil::mapIdTitle())->required();
            }
            if (ModuleManager::getModuleConfig('Member', 'vipEnable', false)) {
                $form->radio('vipId', 'VIP')->options(MemberVipUtil::mapTitle())->required();
                $form->date('vipExpire', 'VIP过期');
            }
        });
        $form->showSubmit(false)->showReset(false);
        return $page->pageTitle('创建用户')->body($form)->handleForm($form, function (Form $form) {
            AdminPermission::demoCheck();
            $data = $form->dataForming();
            $username = !empty($data['username']) ? $data['username'] : null;
            $phone = !empty($data['phone']) ? $data['phone'] : null;
            $email = !empty($data['email']) ? $data['email'] : null;
            $profile = ArrayUtil::keepKeys($data, [
                'nickname',
                'groupId',
                'status',
                'vipId', 'vipExpire',
            ]);
            $ret = MemberUtil::register($username, $phone, $email, $data['password']);
            BizException::throwsIfResponseError($ret);
            if (!empty($profile)) {
                if (isset($profile['vipExpire']) && TimeUtil::isDateEmpty($profile['vipExpire'])) {
                    $profile['vipExpire'] = null;
                }
                MemberUtil::update($ret['data']['id'], $profile);
            }
            EventUtil::fire(new MemberUserRegisteredEvent($ret['data']['id']));
            return Response::redirect(CRUDUtil::jsDialogCloseAndParentGridRefresh());
        });
    }

    public function edit(AdminDialogPage $page)
    {
        $memberUser = ModelUtil::get('member_user', CRUDUtil::id());
        BizException::throwsIfEmpty('用户不存在', $memberUser);
        if (Request::isPost()) {
            AdminPermission::demoCheck();
            $input = InputPackage::buildFromInput();
            switch ($input->getTrimString('_action')) {
                case 'itemCellEdit':
                    $update = [];
                    switch ($input->getTrimString('column')) {
                        case 'status':
                            $update['status'] = $input->getInteger('value');
                            break;
                    }
                    if (!empty($update)) {
                        MemberUtil::update($memberUser['id'], $update);
                    }
                    return Response::generateSuccess();
            }
        }
        $form = Form::make('');
        $form->layoutPanel('基础', function (Form $form) {
            $form->display('id', '用户ID')->addable(true);
            $form->text('username', '用户名');
            $form->text('phone', '手机');
            $form->text('email', '邮箱');
        });
        $form->layoutPanel('高级', function (Form $form) {
            $form->text('nickname', '昵称');
            $form->radio('status', '状态')->optionType(MemberStatus::class)->defaultValue(MemberStatus::NORMAL);
            if (ModuleManager::getModuleConfig('Member', 'groupEnable', false)) {
                $form->radio('groupId', '分组')->options(MemberGroupUtil::mapIdTitle())->required();
            }
            if (ModuleManager::getModuleConfig('Member', 'vipEnable', false)) {
                $form->radio('vipId', 'VIP')->options(MemberVipUtil::mapTitle())->required();
                $form->date('vipExpire', 'VIP过期')->help('VIP过期留空表示永久');
            }
        });
        $form->item($memberUser)->fillFields();
        $form->showSubmit(false)->showReset(false);
        return $page->pageTitle('修改信息')->body($form)->handleForm($form, function (Form $form) use ($memberUser) {
            AdminPermission::demoCheck();
            $data = $form->dataForming();
            $basic = ArrayUtil::keepKeys($data, [
                'username',
                'phone',
                'email',
            ]);
            $profile = ArrayUtil::keepKeys($data, [
                'nickname',
                'groupId',
                'status',
                'vipId', 'vipExpire',
            ]);
            $ret = MemberUtil::updateBasicWithUniqueCheck($memberUser['id'], $basic);
            BizException::throwsIfResponseError($ret);
            if (isset($profile['vipExpire']) && TimeUtil::isDateEmpty($profile['vipExpire'])) {
                $profile['vipExpire'] = null;
            }
            MemberUtil::update($memberUser['id'], $profile);
            return Response::redirect(CRUDUtil::jsDialogCloseAndParentRefresh());
        });
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
                'avatar' => AssetsUtil::fixOrDefault($item['avatar'], 'asset/image/avatar.svg'),
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

    public function delete()
    {
        AdminPermission::demoCheck();
        MemberUtil::delete(CRUDUtil::id());
        return Response::redirect(CRUDUtil::jsGridRefresh());
    }

    public function statusForbidden()
    {
        AdminPermission::demoCheck();
        MemberUtil::updateStatus(CRUDUtil::ids(), MemberStatus::FORBIDDEN);
        return Response::redirect(CRUDUtil::jsGridRefresh());
    }

    public function export(ExportHandle $handle)
    {
        $headTitles = [
            'ID', '用户名', '邮箱', '手机',
            '注册时间', '性别', '姓名', '签名',
        ];
        return $handle
            ->withPageTitle('导出用户信息')
            ->withDefaultExportName('用户信息')
            ->withHeadTitles($headTitles)
            ->handleFetch(function ($page, $pageSize, $search, $param) {
                $query = ModelUtil::model('member_user');
                $query = $query->where(['isDeleted' => false])->orderBy('id', 'desc');
                foreach ($search as $searchItem) {
                    if (!empty($searchItem['id']['eq'])) {
                        $query = $query->where('id', $searchItem['id']['eq']);
                    } elseif (!empty($searchItem['status']['eq'])) {
                        $query = $query->where('status', $searchItem['status']['eq']);
                    } elseif (!empty($searchItem['groupId']['eq'])) {
                        $query = $query->where('groupId', $searchItem['groupId']['eq']);
                    } elseif (!empty($searchItem['vipId']['eq'])) {
                        $query = $query->where('vipId', $searchItem['vipId']['eq']);
                    } elseif (!empty($searchItem['username']['like'])) {
                        $query = $query->where('username', 'like', '%' . $searchItem['username']['like'] . '%');
                    } elseif (!empty($searchItem['email']['like'])) {
                        $query = $query->where('email', 'like', '%' . $searchItem['email']['like'] . '%');
                    } elseif (!empty($searchItem['phone']['like'])) {
                        $query = $query->where('phone', 'like', '%' . $searchItem['phone']['like'] . '%');
                    }
                }
                $result = $query->paginate($pageSize, ['*'], 'page', $page)->toArray();
                $list = [];
                foreach ($result['data'] as $item) {
                    $one = [];
                    $one[] = $item['id'];
                    $one[] = $item['username'];
                    $one[] = $item['email'];
                    $one[] = $item['phone'];
                    $one[] = $item['created_at'];
                    $one[] = TypeUtil::name(Gender::class, $item['gender']);
                    $one[] = $item['realname'];
                    $one[] = $item['signature'];
                    $list[] = $one;
                }
                return [
                    'list' => $list,
                    'total' => $result['total'],
                ];
            })
            ->performCommon();
    }
}
