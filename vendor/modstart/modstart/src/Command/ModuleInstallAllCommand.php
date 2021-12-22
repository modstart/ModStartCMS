<?php

namespace ModStart\Command;

use Illuminate\Console\Command;
use ModStart\Admin\Auth\Admin;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\Response;
use ModStart\Module\ModuleManager;

class ModuleInstallAllCommand extends Command
{
    protected $signature = 'modstart:module-install-all';

    public function handle()
    {
        $this->info("ModuleInstallAll\n");
        foreach (ModuleManager::listAllInstalledModulesInRequiredOrder() as $module) {
            if (!ModuleManager::isExists($module)) {
                continue;
            }
            $ret = ModuleManager::install($module);
            $this->warn(">>> Module $module");
            if (Response::isSuccess($ret)) {
                $this->info($ret['data']['output']);
            } else {
                $this->error($ret['msg']);
            }
            $this->info("");
        }
        $this->warn("ModuleInstallAll Run Finished");

        $initUsers = config('env.MS_INIT_ADMIN_USERS', '');
        if ($initUsers) {
            $initUsers = explode(';', $initUsers);
            $initUsers = array_map(function ($v) {
                list($user, $password) = explode(':', $v);
                $user = trim($user);
                $password = trim($password);
                if (empty($user) || empty($password)) {
                    return null;
                }
                return [
                    'user' => $user,
                    'password' => $password,
                ];
            }, $initUsers);
            $initUsers = array_filter($initUsers);
            if (!empty($initUsers)) {
                if (ModelUtil::count('admin_user') <= 0) {
                    foreach ($initUsers as $initUser) {
                        Admin::add($initUser['user'], $initUser['password']);
                    }
                }
            }
        }

    }

}
