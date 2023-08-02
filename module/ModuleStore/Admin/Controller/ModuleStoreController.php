<?php


namespace Module\ModuleStore\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Admin\Layout\AdminConfigBuilder;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Core\Util\FileUtil;
use ModStart\Core\Util\ReUtil;
use ModStart\Form\Form;
use ModStart\Module\ModuleManager;
use ModStart\Repository\RepositoryUtil;
use Module\ModuleStore\Util\ModuleStoreUtil;

class ModuleStoreController extends Controller
{
    public function index()
    {
        AdminPermission::permitCheck('ModuleStoreManage');
        return view('module::ModuleStore.View.admin.moduleStore.index');
    }

    public function all()
    {
        AdminPermission::permitCheck('ModuleStoreManage');
        return Response::generateSuccessData(ModuleStoreUtil::all());
    }

    private function moduleOperateCheck($module)
    {
        BizException::throwsIf('当前环境禁止「模块管理」相关操作', config('env.MS_MODULE_STORE_DISABLE', false));
        $whitelist = config('env.MS_MODULE_WHITELIST', '');
        if (empty($whitelist)) {
            return;
        }
        $whitelist = array_map(function ($v) {
            return trim($v);
        }, explode(',', $whitelist));
        $whitelist = array_filter($whitelist);
        if (empty($whitelist)) {
            return;
        }
        $passed = false;
        foreach ($whitelist as $item) {
            if (ReUtil::isWildMatch($item, $module)) {
                $passed = true;
                break;
            }
        }
        BizException::throwsIf('只允许操作模块:' . join(',', $whitelist), !$passed);
    }

    private function doFinish($msgs)
    {
        return Response::generateSuccessData([
            'msg' => array_map(function ($item) {
                return '<i class="iconfont icon-hr"></i> ' . $item;
            }, $msgs),
            'finish' => true,
        ]);
    }

    private function doNext($command, $step, $msgs = [], $data = [])
    {
        $input = InputPackage::buildFromInput();
        $data = array_merge($input->getJsonAsInput('data')->all(), $data);
        return Response::generateSuccessData([
            'msg' => array_map(function ($item) {
                if (!starts_with($item, '<')) {
                    $item = "<span class='ub-text-white'>$item</span>";
                }
                return '<i class="iconfont icon-hr"></i> ' . $item;
            }, $msgs),
            'command' => $command,
            'step' => $step,
            'data' => $data,
            'finish' => false,
        ]);
    }

    public function disable()
    {
        AdminPermission::permitCheck('ModuleStoreManage');
        AdminPermission::demoCheck();
        $input = InputPackage::buildFromInput();
        $step = $input->getTrimString('step');
        $dataInput = $input->getJsonAsInput('data');
        $module = $dataInput->getTrimString('module');
        $version = $dataInput->getTrimString('version');
        BizException::throwsIfEmpty('module为空', $module);
        BizException::throwsIfEmpty('version为空', $version);
        $this->moduleOperateCheck($module);
        switch ($step) {
            default:
                $ret = ModuleManager::disable($module);
                BizException::throwsIfResponseError($ret);
                return $this->doFinish([
                    '<span class="ub-text-success">禁用成功，请 <a href="javascript:;" onclick="parent.location.reload()">刷新后台</a> 查看最新系统</span>',
                ]);
        }
    }

    public function enable()
    {
        AdminPermission::permitCheck('ModuleStoreManage');
        AdminPermission::demoCheck();
        $input = InputPackage::buildFromInput();
        $step = $input->getTrimString('step');
        $dataInput = $input->getJsonAsInput('data');
        $module = $dataInput->getTrimString('module');
        $version = $dataInput->getTrimString('version');
        BizException::throwsIfEmpty('module为空', $module);
        BizException::throwsIfEmpty('version为空', $version);
        $this->moduleOperateCheck($module);
        switch ($step) {
            default:
                $ret = ModuleManager::enable($module);
                BizException::throwsIfResponseError($ret);
                return $this->doFinish([
                    '<span class="ub-text-success">启动成功，请 <a href="javascript:;" onclick="parent.location.reload()">刷新后台</a> 查看最新系统</span>',
                ]);
        }
    }

    public function uninstall()
    {
        AdminPermission::permitCheck('ModuleStoreManage');
        AdminPermission::demoCheck();
        $input = InputPackage::buildFromInput();
        $step = $input->getTrimString('step');
        $dataInput = $input->getJsonAsInput('data');
        $module = $dataInput->getTrimString('module');
        $version = $dataInput->getTrimString('version');
        $isLocal = $dataInput->getBoolean('isLocal');
        BizException::throwsIfEmpty('module为空', $module);
        BizException::throwsIfEmpty('version为空', $version);
        BizException::throwsIf('系统模块不能动态设置', ModuleManager::isSystemModule($module));
        $this->moduleOperateCheck($module);
        if ($isLocal) {
            switch ($step) {
                default:
                    $ret = ModuleManager::uninstall($module);
                    BizException::throwsIfResponseError($ret);
                    return $this->doFinish([
                        '<span class="ub-text-success">卸载完成，请 <a href="javascript:;" onclick="parent.location.reload()">刷新后台</a> 查看最新系统</span>',
                    ]);
            }
        } else {
            switch ($step) {
                case 'removePackage':
                    $ret = ModuleStoreUtil::removeModule($module, $version);
                    BizException::throwsIfResponseError($ret);
                    return $this->doFinish([
                        '<span class="ub-text-success">卸载完成，请 <a href="javascript:;" onclick="parent.location.reload()">刷新后台</a> 查看最新系统</span>',
                    ]);
                default:
                    $ret = ModuleManager::uninstall($module);
                    BizException::throwsIfResponseError($ret);
                    return $this->doNext('uninstall', 'removePackage', [
                        '<span class="ub-text-success">开始卸载 ' . $module . ' V' . $version . '</span>',
                    ]);

            }
        }
    }

    public function upgrade()
    {
        AdminPermission::permitCheck('ModuleStoreManage');
        AdminPermission::demoCheck();
        $input = InputPackage::buildFromInput();
        $token = $input->getTrimString('token');
        $step = $input->getTrimString('step');
        BizException::throwsIfEmpty('请先登录ModStartCMS账号', $token);
        $dataInput = $input->getJsonAsInput('data');
        $module = $dataInput->getTrimString('module');
        $version = $dataInput->getTrimString('version');
        BizException::throwsIfEmpty('module为空', $module);
        BizException::throwsIfEmpty('version为空', $version);
        $this->moduleOperateCheck($module);

        switch ($step) {
            case 'installModule':
                $ret = ModuleManager::install($module, true);
                BizException::throwsIfResponseError($ret);
                $ret = ModuleManager::enable($module);
                BizException::throwsIfResponseError($ret);
                return $this->doFinish([
                    '<span class="ub-text-success">升级安装完成，请 <a href="javascript:;" onclick="parent.location.reload()">刷新后台</a> 查看最新系统</span>',
                ]);
            case 'unpackPackage':
                $package = $dataInput->getTrimString('package');
                BizException::throwsIfEmpty('package为空', $package);
                $licenseKey = $dataInput->getTrimString('licenseKey');
                BizException::throwsIfEmpty('licenseKey为空', $licenseKey);
                try {
                    $ret = ModuleStoreUtil::unpackModule($module, $package, $licenseKey);
                } catch (\Exception $e) {
                    ModuleStoreUtil::cleanDownloadedPackage($package);
                    throw $e;
                }
                BizException::throwsIfResponseError($ret);
                return $this->doNext('upgrade', 'installModule', array_merge([
                    '<span class="ub-text-success">模块解压完成</span>',
                    '<span class="ub-text-white">开始安装...</span>',
                ], $ret['data']));
            case 'downloadPackage':
                $ret = ModuleStoreUtil::downloadPackage($token, $module, $version);
                BizException::throwsIfResponseError($ret);
                return $this->doNext('upgrade', 'unpackPackage', [
                    '<span class="ub-text-success">获取安装包完成，大小 ' . FileUtil::formatByte($ret['data']['packageSize']) . '</span>',
                    '<span class="ub-text-white">开始解压安装包...</span>'
                ], [
                    'package' => $ret['data']['package'],
                    'licenseKey' => $ret['data']['licenseKey'],
                ]);
            case 'checkPackage':
                $ret = ModuleStoreUtil::checkPackage($token, $module, $version);
                if (Response::isError($ret)) {
                    return $ret;
                }
                $msgs = [];
                foreach ($ret['data']['requires'] as $require) {
                    $msgs[] = '<span>&nbsp;&nbsp;</span>'
                        . ($require['success']
                            ? '<span class="ub-text-success"><i class="iconfont icon-check"></i> 成功</span>'
                            : '<span class="ub-text-danger"><i class="iconfont icon-warning"></i> 失败</span>')
                        . " <span>$require[name]</span> " . ($require['resolve'] ? " <span>解决：$require[resolve]</span>" : "");
                }
                if ($ret['data']['errorCount'] > 0) {
                    return $this->doFinish(array_merge($msgs, [
                        '<span class="ub-text-danger">预检失败，' . $ret['data']['errorCount'] . '个依赖不满足要求</span>',
                    ]));
                }
                $msgs[] = '<span class="ub-text-white">开始下载安装包...</span>';
                return $this->doNext('upgrade', 'downloadPackage', array_merge([
                    'PHP版本: v' . PHP_VERSION,
                    '<span class="ub-text-success">预检成功，' . count($ret['data']['requires']) . '个依赖满足要求，安装包大小 ' . FileUtil::formatByte($ret['data']['packageSize']) . '</span>',
                ], $msgs));
            default:
                return $this->doNext('upgrade', 'checkPackage', [
                    '<span class="ub-text-success">开始升级到远程模块 ' . $module . ' V' . $version . '</span>',
                    '<span class="ub-text-white">开始模块安装预检...</span>'
                ]);
        }
    }

    public function install()
    {
        AdminPermission::permitCheck('ModuleStoreManage');
        AdminPermission::demoCheck();
        $input = InputPackage::buildFromInput();
        $token = $input->getTrimString('token');
        $step = $input->getTrimString('step');
        BizException::throwsIfEmpty('请先登录ModStartCMS账号', $token);
        $dataInput = $input->getJsonAsInput('data');
        $module = $dataInput->getTrimString('module');
        $version = $dataInput->getTrimString('version');
        $isLocal = $dataInput->getBoolean('isLocal');
        BizException::throwsIfEmpty('module为空', $module);
        BizException::throwsIfEmpty('version为空', $version);
        BizException::throwsIf('系统模块不能动态设置', ModuleManager::isSystemModule($module));
        $this->moduleOperateCheck($module);

        if ($isLocal) {
            switch ($step) {
                case 'installModule':
                    $ret = ModuleManager::install($module, true);
                    BizException::throwsIfResponseError($ret);
                    $ret = ModuleManager::enable($module);
                    BizException::throwsIfResponseError($ret);
                    return $this->doFinish([
                        '<span class="ub-text-success">安装完成，请 <a href="javascript:;" onclick="parent.location.reload()">刷新后台</a> 查看最新系统</span>',
                    ]);
                default:
                    return $this->doNext('install', 'installModule', [
                        '<span class="ub-text-success">开始安装本地模块 ' . $module . ' V' . $version . '</span>',
                        '<span class="ub-text-white">开始安装..</span>'
                    ]);
            }
        } else {
            switch ($step) {
                case 'installModule':
                    $ret = ModuleManager::install($module, true);
                    if (Response::isError($ret)) {
                        ModuleManager::clean($module);
                        BizException::throws($ret['msg']);
                    }
                    $ret = ModuleManager::enable($module);
                    BizException::throwsIfResponseError($ret);
                    return $this->doFinish([
                        '<span class="ub-text-success">安装完成，请 <a href="javascript:;" onclick="parent.location.reload()">刷新后台</a> 查看最新系统</span>',
                    ]);
                case 'unpackPackage':
                    $package = $dataInput->getTrimString('package');
                    BizException::throwsIfEmpty('package为空', $package);
                    $licenseKey = $dataInput->getTrimString('licenseKey');
                    BizException::throwsIfEmpty('licenseKey为空', $licenseKey);
                    try {
                        $ret = ModuleStoreUtil::unpackModule($module, $package, $licenseKey);
                    } catch (\Exception $e) {
                        ModuleStoreUtil::cleanDownloadedPackage($package);
                        throw $e;
                    }
                    BizException::throwsIfResponseError($ret);
                    return $this->doNext('install', 'installModule', array_merge([
                        '<span class="ub-text-success">模块解压完成</span>',
                        '<span class="ub-text-white">开始安装...</span>',
                    ], $ret['data']));
                case 'downloadPackage':
                    $ret = ModuleStoreUtil::downloadPackage($token, $module, $version);
                    BizException::throwsIfResponseError($ret);
                    return $this->doNext('install', 'unpackPackage', [
                        '<span class="ub-text-success">获取安装包完成，大小 ' . FileUtil::formatByte($ret['data']['packageSize']) . '</span>',
                        '<span class="ub-text-white">开始解压安装包...</span>'
                    ], [
                        'package' => $ret['data']['package'],
                        'licenseKey' => $ret['data']['licenseKey'],
                    ]);
                case 'checkPackage':
                    $ret = ModuleStoreUtil::checkPackage($token, $module, $version);
                    if (Response::isError($ret)) {
                        return $ret;
                    }
                    $msgs = [];
                    foreach ($ret['data']['requires'] as $require) {
                        $msgs[] = '<span>&nbsp;&nbsp;</span>'
                            . ($require['success']
                                ? '<span class="ub-text-success"><i class="iconfont icon-check"></i> 成功</span>'
                                : '<span class="ub-text-danger"><i class="iconfont icon-warning"></i> 失败</span>')
                            . " <span>$require[name]</span> " . ($require['resolve'] ? " <span>解决：$require[resolve]</span>" : "");
                    }
                    if ($ret['data']['errorCount'] > 0) {
                        return $this->doFinish(array_merge($msgs, [
                            '<span class="ub-text-danger">预检失败，' . $ret['data']['errorCount'] . '个依赖不满足要求</span>',
                        ]));
                    }
                    $msgs[] = '<span class="ub-text-white">开始下载安装包...</span>';
                    return $this->doNext('install', 'downloadPackage', array_merge([
                        'PHP版本: v' . PHP_VERSION,
                        '<span class="ub-text-success">预检成功，' . count($ret['data']['requires']) . '个依赖满足要求，安装包大小 ' . FileUtil::formatByte($ret['data']['packageSize']) . '</span>',
                    ], $msgs));
                    break;
                default:
                    return $this->doNext('install', 'checkPackage', [
                        '<span class="ub-text-success">开始安装远程模块 ' . $module . ' V' . $version . '</span>',
                        '<span class="ub-text-white">开始模块安装预检...</span>'
                    ]);
            }
        }
    }

    public function config(AdminConfigBuilder $builder, $module)
    {
        AdminPermission::permitCheck('ModuleStoreManage');
        $basic = ModuleManager::getModuleBasic($module);
        $builder->useDialog();
        $builder->pageTitle(htmlspecialchars($basic['title']) . ' ' . L('Module Config'));
        $builder->layoutHtml('<div class="ub-alert danger"><i class="iconfont icon-warning"></i> 本页面包含的配置仅供开发使用，设置不当可能会导致系统功能异常</div>');
        $moduleInfo = ModuleManager::getInstalledModuleInfo($module);
        BizException::throwsIfEmpty('Module config error', $basic['config']);
        foreach ($basic['config'] as $key => $callers) {
            $field = null;
            if (!isset($moduleInfo['config'][$key])) {
                $moduleInfo['config'][$key] = null;
            }
            foreach ($callers as $caller) {
                $name = array_shift($caller);
                if (null === $field) {
                    array_unshift($caller, $key);
                    $field = call_user_func([$builder, $name], ...$caller);
                } else {
                    call_user_func([$field, $name], ...$caller);
                }
            }
        }
        return $builder->perform(RepositoryUtil::itemFromArray($moduleInfo['config']), function (Form $form) use ($module, $moduleInfo) {
            AdminPermission::demoCheck();
            if ($moduleInfo['isSystem']) {
                ModuleManager::saveSystemOverwriteModuleConfig($module, $form->dataForming());
            } else {
                ModuleManager::saveUserInstalledModuleConfig($module, $form->dataForming());
            }
            return Response::generate(0, '保存成功', null, CRUDUtil::jsDialogClose());
        });
    }

}
