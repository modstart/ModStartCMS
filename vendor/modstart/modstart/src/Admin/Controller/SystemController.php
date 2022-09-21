<?php


namespace ModStart\Admin\Controller;


use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use ModStart\Admin\Auth\Admin;
use ModStart\Admin\Auth\AdminPermission;
use ModStart\Admin\Layout\AdminDialogPage;
use ModStart\Core\Dao\DynamicModel;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\RandomUtil;
use ModStart\Form\Form;
use ModStart\ModStart;

class SystemController extends Controller
{
    public function clearCache()
    {
        AdminPermission::demoCheck();
        AdminPermission::permitCheck('SystemManage');
        Admin::addInfoLog(Admin::id(), L('Clear Cache'));
        $exitCode = Artisan::call("cache:clear");
        if (0 != $exitCode) {
            return Response::send(-1, L('Clear Cache') . ' ' . L('Error') . " cache:clear ExitCode($exitCode)");
        }
        $exitCode = Artisan::call("view:clear");
        if (0 != $exitCode) {
            return Response::send(-1, L('Clear Cache') . ' ' . L('Error') . " view:clear ExitCode($exitCode)");
        }
        ModStart::clearCache();
        return Response::jsonSuccess(L('Operate Success'));
    }

    public function securityFix(AdminDialogPage $page)
    {
        AdminPermission::permitCheck('SystemManage');
        $input = InputPackage::buildFromInput();
        $type = $input->getTrimString('type');
        switch ($type) {
            case 'installLock':
                AdminPermission::demoCheck();
                if (!file_exists(storage_path('install.lock'))) {
                    file_put_contents(storage_path('install.lock'), 'ok');
                }
                // check write failed
                if (!file_exists(storage_path('install.lock'))) {
                    return Response::json(-1, L('Operate Failed'));
                }
                return Response::json(0, L('Operate Success'), null, '[reload]');
            case 'installScript':
                AdminPermission::demoCheck();
                if (file_exists(public_path('install.php'))) {
                    @unlink(public_path('install.php'));
                }
                // check delete failed
                if (file_exists(public_path('install.php'))) {
                    return Response::json(-1, L('Operate Failed'));
                }
                return Response::json(0, L('Operate Success'), null, '[reload]');
            case 'appDebug':
                AdminPermission::demoCheck();
                $content = file_get_contents(base_path('.env'));
                $content = preg_replace('/APP_DEBUG\\s*=\\s*true/', 'APP_DEBUG=false', $content);
                file_put_contents(base_path('.env'), $content);
                return Response::json(0, L('Operate Success'), null, '[reload]');
            case 'adminPath':
                $form = new Form(DynamicModel::class);
                $form->text('oldPath', L('Current Path'))->rules('required')
                    ->value(config('env.ADMIN_PATH', '/admin/'))->readonly(true);
                $form->text('newPath', L('New Path'))->rules('required')
                    ->value('/admin_' . RandomUtil::lowerReadableString(6) . '/');
                $form->showSubmit(false)->showReset(false);
                if (Request::isPost()) {
                    return $form->formRequest(function (Form $form) {
                        AdminPermission::demoCheck();
                        $data = $form->dataForming();
                        $newPath = $data['newPath'];
                        if (!Str::startsWith($newPath, '/') || !Str::endsWith($newPath, '/')) {
                            return Response::generateError(L('Url must start with / and end with /'));
                        }
                        if (!preg_match('/^\\/[a-zA-Z0-9_]+\\/$/', $newPath)) {
                            return Response::generateError(L('Admin url only contains a-zA-Z0-9_'));
                        }
                        $content = file_get_contents(base_path('.env'));
                        $content = preg_replace('/ADMIN_PATH\\s*=\\s*.*?\\n/', "ADMIN_PATH=$newPath\n", $content);
                        try {
                            file_put_contents(base_path('.env'), $content);
                            return Response::json(0, L('Operate Success'), null, '[js]parent.location.href="' . $newPath . '"');
                        } catch (\Exception $e) {
                            return Response::jsonError(L('No Permission'));
                        }
                    });
                }
                return $page->pageTitle(L('Change Admin Url'))->body($form);
        }
        return Response::sendError('Unknown Type');
    }
}
