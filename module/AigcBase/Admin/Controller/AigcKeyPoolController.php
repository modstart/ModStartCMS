<?php


namespace Module\AigcBase\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminConfigBuilder;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Type\TypeUtil;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Field\AbstractField;
use ModStart\Field\AutoRenderedFieldValue;
use ModStart\Form\Form;
use ModStart\Grid\Displayer\ItemOperate;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use ModStart\Widget\ButtonDialogRequest;
use ModStart\Widget\TextAjaxRequest;
use Module\AigcBase\Model\AigcKeyPool;
use Module\AigcBase\Provider\AbstractAigcChatProvider;
use Module\AigcBase\Provider\AigcChatProvider;
use Module\AigcBase\Provider\AigcProvider;
use Module\AigcBase\Type\AigcKeyPoolStatus;
use Module\AigcBase\Type\AigcProviderType;
use Module\AigcBase\Util\AigcKeyPoolUtil;

class AigcKeyPoolController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init(AigcKeyPool::class)
            ->field(function ($builder) {
                /** @var HasFields $builder */
                $builder->id('id', 'ID');
                $typeOptions = AigcProvider::allMap();
                foreach ($typeOptions as $k => $v) {
                    $provider = AigcProvider::getByName($k);
                    $typeOptions[$k] = join('-', [
                        TypeUtil::name(AigcProviderType::class, $provider->type()),
                        $provider->title()
                    ]);
                }
                $typeField = $builder->radio('type', '类型')
                    ->required()
                    ->options($typeOptions);
                if (empty($typeOptions)) {
                    $typeField->help('<div class="ub-alert warning">暂未安装模型驱动，请在 系统管理→模块管理→模块市场 安装模型驱动</div>');
                }
                $whenHelps = [];
                foreach (AigcProvider::listAll() as $provider) {
                    $typeField->when('=', $provider->name(), function ($builder) use (&$provider) {
                        $provider->paramForm($builder);
                    });
                    $whenHelps[$provider->name()] = $provider->help();
                }
                $typeField->whenHelps($whenHelps);
                if (Request::isPost()) {
                    if (empty($typeOptions)) {
                        BizException::throws('暂未安装模型驱动，请在 系统管理→模块管理→模块市场 安装模型驱动');
                    }
                }
                $builder->radio('status', '状态')->options(AigcKeyPoolStatus::editList())->defaultValue(AigcKeyPoolStatus::ONLINE);
                $builder->number('priority', '权重')->defaultValue(10)->help('数字越大被实用的概率越大');
                // $builder->display('callCount', '调用次数')->listable(true);
                //                $builder->display('successCount', '成功次数')->listable(true);
                //                $builder->display('failCount', '失败次数')->listable(true);
                //                $builder->display('lastCallTime', '上次调用时间')->listable(true);
                $builder->display('_data', '数据统计')
                    ->hookRendering(function (AbstractField $field, $item, $index) {
                        $html = [];
                        $html[] = "<table class='ub-table mini border tw-bg-white'>";
                        $html[] = '<tr><td>调用次数</td><td>' . ($item->callCount + 0) . '</td></tr>';
                        $html[] = '<tr><td>成功次数</td><td>' . ($item->successCount + 0) . '</td></tr>';
                        $html[] = '<tr><td>失败次数</td><td>' . ($item->failCount + 0) . '</td></tr>';
                        $html[] = '<tr><td>上次调用时间</td><td>' . ($item->lastCallTime ? date('Y-m-d H:i:s', strtotime($item->lastCallTime)) : '-') . '</td></tr>';
                        $html[] = '</table>';
                        return AutoRenderedFieldValue::make(join('', $html));
                    })
                    ->width(300)
                    ->listable(true)->showable(true)->addable(false)->editable(false);
                $builder->display('_param', '参数')
                    ->hookRendering(function (AbstractField $field, $item, $index) {
                        $html = [];
                        $provider = AigcProvider::getByName($item->type);
                        if ($provider) {
                            $html[] = "<table class='ub-table mini border tw-bg-white'>";
                            foreach ($provider->paramDisplay($item->toArray()) as $v) {
                                $html[] = "<tr><td>$v[name]</td><td><div style='max-width:8rem;overflow:hidden;text-overflow:ellipsis;'>$v[value]</div></td></tr>";
                            }
                            $html[] = "</table>";
                        }
                        return AutoRenderedFieldValue::make(join('', $html));
                    })
                    ->width(300)
                    ->listable(true)->showable(true)->addable(false)->editable(false);
                $builder->text('remark', '备注');

                $builder->display('created_at', L('Created At'))->listable(false);
                $builder->display('updated_at', L('Updated At'))->listable(false);
            })
            ->gridFilter(function (GridFilter $filter) {
                // $filter->eq('userId', '用户ID');
                // $filter->eq('appId', '技能')->selectModel('aigc_app', 'id', 'title');
            })
            ->gridOperateAppend(
                ButtonDialogRequest::make('primary', '<i class="iconfont icon-cog"></i> 功能设置', modstart_admin_url('aigc/key_pool/config'))->size('big')
            )
            ->hookItemOperateRendering(function (ItemOperate $itemOperate) {
                $item = $itemOperate->item();
                $provider = AigcProvider::getByName($item->type);
                if (in_array($provider->type(), [AigcProviderType::CHAT])) {
                    $itemOperate->prepend(
                        TextAjaxRequest::make('primary', '测试', modstart_admin_url('aigc/key_pool/test', ['_id' => $item->id]))
                            ->attr('data-ajax-request-loading')
                    );
                }
            })
            ->hookChanged(function (Form $form) {
                AigcKeyPoolUtil::clearCache();
            })
            ->title('AI平台对接');
    }

    public function test()
    {
        AdminPermission::demoCheck();
        $record = ModelUtil::get(AigcKeyPool::class, CRUDUtil::id());
        BizException::throwsIfEmpty('记录不存在', $record);
        ModelUtil::decodeRecordJson($record, ['param']);
        if (empty($record['param']['model'])) {
            $record['param']['model'] = 'default';
        }
        $provider = AigcProvider::getByFullName($record['type'] . ':' . $record['param']['model']);
        BizException::throwsIfEmpty('模型驱动不存在', $provider);
        $ret = Response::generateError('暂不支持测试');
        switch ($provider->type()) {
            case AigcProviderType::CHAT:
                /** @var AbstractAigcChatProvider $chatProvider */
                $chatProvider = $provider;
                $ret = $chatProvider->chat('test', '你是什么模型，使用最简短的回答');
                break;
        }
        if (!empty($ret['data']['isError'])) {
            return Response::generateError('模型测试失败');
        }
        if (Response::isSuccess($ret)) {
            return Response::generateSuccess('测试成功');
        }
        return $ret;
    }

    public function config(AdminConfigBuilder $builder)
    {
        $builder->useDialog();
        $builder->pageTitle('功能设置');
        $builder->switch('AigcBase_AdminRichEditorEnable', '后台富文本编辑器AI功能')->defaultValue(false);
        $builder->select('AigcBase_AdminRichEditorDriver', '后台富文本编辑器AI驱动')->options(AigcChatProvider::modelMap());
        $builder->select('AigcBase_AdminDefaultChatDriver', '后台默认AI对话驱动')->options(AigcChatProvider::modelMap());
        $builder->formClass('wide-lg');
        $builder->disableBoxWrap(true);
        return $builder->perform();
    }
}
