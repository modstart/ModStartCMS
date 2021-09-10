<?php


namespace Module\ModuleStore\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Admin\Auth\Admin;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Admin\Layout\AdminConfigBuilder;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Core\Util\FileUtil;
use ModStart\Form\Form;
use ModStart\Module\ModuleManager;
use ModStart\Repository\RepositoryUtil;
use Module\ModuleStore\Util\ModuleStoreUtil;

class ModuleStoreController extends Controller
{
    public function __construct()
    {
        AdminPermission::permitCheck('ModuleStoreManage');
    }

    public function index()
    {
        return view('module::ModuleStore.View.admin.moduleStore.index');
    }

    public function all()
    {
        return Response::generateSuccessData(ModuleStoreUtil::all());
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
        AdminPermission::demoCheck();
        $input = InputPackage::buildFromInput();
        $step = $input->getTrimString('step');
        $dataInput = $input->getJsonAsInput('data');
        $module = $dataInput->getTrimString('module');
        $version = $dataInput->getTrimString('version');
        BizException::throwsIfEmpty('module为空', $module);
        BizException::throwsIfEmpty('version为空', $version);
        switch ($step) {
            default:
                $ret = ModuleManager::disable($module);
                BizException::throwsIfResponseError($ret);
                return $this->doFinish([
                    '<span class="ub-text-success">禁用成功，请 <a href="javascript:;" onclick="window.location.reload()">刷新后台</a> 查看最新系统</span>',
                ]);
        }
    }

    public function enable()
    {
        AdminPermission::demoCheck();
        $input = InputPackage::buildFromInput();
        $step = $input->getTrimString('step');
        $dataInput = $input->getJsonAsInput('data');
        $module = $dataInput->getTrimString('module');
        $version = $dataInput->getTrimString('version');
        BizException::throwsIfEmpty('module为空', $module);
        BizException::throwsIfEmpty('version为空', $version);
        switch ($step) {
            default:
                $ret = ModuleManager::enable($module);
                BizException::throwsIfResponseError($ret);
                return $this->doFinish([
                    '<span class="ub-text-success">启动成功，请 <a href="javascript:;" onclick="window.location.reload()">刷新后台</a> 查看最新系统</span>',
                ]);
        }
    }

    public function uninstall()
    {
        AdminPermission::demoCheck();
        $input = InputPackage::buildFromInput();
        $step = $input->getTrimString('step');
        $dataInput = $input->getJsonAsInput('data');
        $module = $dataInput->getTrimString('module');
        $version = $dataInput->getTrimString('version');
        $isLocal = $dataInput->getBoolean('isLocal');
        BizException::throwsIfEmpty('module为空', $module);
        BizException::throwsIfEmpty('version为空', $version);
        BizException::throwsIf('系统模块不能动态配置', ModuleManager::isSystemModule($module));
        if ($isLocal) {
            switch ($step) {
                default:
                    $ret = ModuleManager::uninstall($module);
                    BizException::throwsIfResponseError($ret);
                    return $this->doFinish([
                        '<span class="ub-text-success">卸载完成，请 <a href="javascript:;" onclick="window.location.reload()">刷新后台</a> 查看最新系统</span>',
                    ]);
            }
        } else {
            switch ($step) {
                case 'removePackage':
                    $ret = ModuleStoreUtil::removeModule($module, $version);
                    BizException::throwsIfResponseError($ret);
                    return $this->doFinish([
                        '<span class="ub-text-success">卸载完成，请 <a href="javascript:;" onclick="window.location.reload()">刷新后台</a> 查看最新系统</span>',
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
                switch ($step) {
            case 'installModule':
                $ret = ModuleManager::install($module, true);
                BizException::throwsIfResponseError($ret);
                $ret = ModuleManager::enable($module);
                BizException::throwsIfResponseError($ret);
                return $this->doFinish([
                    '<span class="ub-text-success">升级安装完成，请 <a href="javascript:;" onclick="window.location.reload()">刷新后台</a> 查看最新系统</span>',
                ]);
            case 'unpackPackage':
                $package = $dataInput->getTrimString('package');
                BizException::throwsIfEmpty('package为空', $package);
                $licenseKey = $dataInput->getTrimString('licenseKey');
                BizException::throwsIfEmpty('licenseKey为空', $licenseKey);
                $ret = ModuleStoreUtil::unpackModule($module, $package, $licenseKey);
                BizException::throwsIfResponseError($ret);
                return $this->doNext('upgrade', 'installModule', [
                    '<span class="ub-text-success">模块解压完成</span>',
                    '<span class="ub-text-default">开始安装...</span>',
                ]);
            case 'downloadPackage':
                $ret = ModuleStoreUtil::downloadPackage($token, $module, $version);
                BizException::throwsIfResponseError($ret);
                return $this->doNext('upgrade', 'unpackPackage', [
                    '<span class="ub-text-success">获取安装包完成，大小 ' . FileUtil::formatByte($ret['data']['packageSize']) . '</span>',
                    '<span class="ub-text-default">开始解压安装包...</span>'
                ], [
                    'package' => $ret['data']['package'],
                    'licenseKey' => $ret['data']['licenseKey'],
                ]);
            default:
                return $this->doNext('upgrade', 'downloadPackage', [
                    '<span class="ub-text-success">开始升级到远程模块 ' . $module . ' V' . $version . '</span>',
                    '<span class="ub-text-default">开始获取模块安装包...</span>'
                ]);
        }
    }

    public function install()
    {
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
        BizException::throwsIf('系统模块不能动态配置', ModuleManager::isSystemModule($module));
        if ($isLocal) {
            switch ($step) {
                case 'installModule':
                    $ret = ModuleManager::install($module, true);
                    BizException::throwsIfResponseError($ret);
                    $ret = ModuleManager::enable($module);
                    BizException::throwsIfResponseError($ret);
                    return $this->doFinish([
                        '<span class="ub-text-success">安装完成，请 <a href="javascript:;" onclick="window.location.reload()">刷新后台</a> 查看最新系统</span>',
                    ]);
                default:
                    return $this->doNext('install', 'installModule', [
                        '<span class="ub-text-success">开始安装本地模块 ' . $module . ' V' . $version . '</span>',
                        '<span class="ub-text-default">开始安装..</span>'
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
                        '<span class="ub-text-success">安装完成，请 <a href="javascript:;" onclick="window.location.reload()">刷新后台</a> 查看最新系统</span>',
                    ]);
                case 'unpackPackage':
                    $package = $dataInput->getTrimString('package');
                    BizException::throwsIfEmpty('package为空', $package);
                    $licenseKey = $dataInput->getTrimString('licenseKey');
                    BizException::throwsIfEmpty('licenseKey为空', $licenseKey);
                    $ret = ModuleStoreUtil::unpackModule($module, $package, $licenseKey);
                    BizException::throwsIfResponseError($ret);
                    return $this->doNext('install', 'installModule', [
                        '<span class="ub-text-success">模块解压完成</span>',
                        '<span class="ub-text-default">开始安装...</span>',
                    ]);
                case 'downloadPackage':
                    $ret = ModuleStoreUtil::downloadPackage($token, $module, $version);
                    BizException::throwsIfResponseError($ret);
                    return $this->doNext('install', 'unpackPackage', [
                        '<span class="ub-text-success">获取安装包完成，大小 ' . FileUtil::formatByte($ret['data']['packageSize']) . '</span>',
                        '<span class="ub-text-default">开始解压安装包...</span>'
                    ], [
                        'package' => $ret['data']['package'],
                        'licenseKey' => $ret['data']['licenseKey'],
                    ]);
                default:
                    return $this->doNext('install', 'downloadPackage', [
                        '<span class="ub-text-success">开始安装远程模块 ' . $module . ' V' . $version . '</span>',
                        '<span class="ub-text-default">开始获取模块安装包...</span>'
                    ]);
            }
        }
    }

    public function config(AdminConfigBuilder $builder, $module)
    {
        $basic = ModuleManager::getModuleBasic($module);
        AdminPermission::demoPostCheck();
        $builder->useDialog();
        $builder->pageTitle($basic['title'] . ' ' . L('Module Config'));
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
        return $builder->perform(RepositoryUtil::itemFromArray($moduleInfo['config']), function (Form $form) use ($module) {
            ModuleManager::saveUserInstalledModuleConfig($module, $form->dataForming());
            return Response::generate(0, '保存成功', null, CRUDUtil::jsDialogClose());
        });
    }

}
