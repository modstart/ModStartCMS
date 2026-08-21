<?php

namespace ModStart\Command;

use Illuminate\Console\Command;
use ModStart\Admin\Auth\Admin;
use ModStart\Admin\Model\AdminUser;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\FileUtil;
use ModStart\ModStart;
use ModStart\Module\ModuleManager;

class ModuleInstallAllCommand extends Command
{
    protected $signature = 'modstart:module-install-all {--link-asset}';

    public function handle()
    {
        $failed = false;
        $linkAsset = $this->option('link-asset');
        $this->info("ModuleInstallAll\n");

        $assetDir = public_path('asset');
        $assetCount = 0;
        if (is_dir($assetDir)) {
            $files = FileUtil::listAllFiles($assetDir);
            foreach ($files as $file) {
                if ($file['isFile']) {
                    $assetCount++;
                }
            }
        }
        if ($assetCount <= 0) {
            $this->error('vendor:publish published nothing to public/asset');
            return 1;
        }
        $this->info('public/asset files: ' . $assetCount);

        foreach (ModuleManager::listAllInstalledModulesInRequiredOrder() as $module) {
            if (!ModuleManager::isExists($module)) {
                continue;
            }
            $this->warn(">>> Module $module Installing");
            $ret = ModuleManager::install($module, false, [
                'linkAsset' => $linkAsset,
            ]);
            if (Response::isSuccess($ret)) {
                $this->info($ret['data']['output']);
            } else {
                $this->error($ret['msg']);
                $failed = true;
            }
            $this->info("");
        }

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
                if (ModelUtil::count(AdminUser::class) <= 0) {
                    foreach ($initUsers as $initUser) {
                        Admin::add($initUser['user'], $initUser['password']);
                        $this->warn(">>> Init User: {$initUser['user']}");
                    }
                }
            }
        }

        $this->publishHotfixFiles();

        if ($failed) {
            $this->error("ModuleInstallAll Run Finished With Errors");
            return 1;
        }

        $this->warn("ModuleInstallAll Run Finished");

        return 0;
    }

    private function publishHotfixFiles()
    {
        $this->warn(">>> Publish Hotfix");
        $env = ModStart::env();
        $dir = rtrim(base_path('vendor/modstart/modstart/resources/hot_fix'), '/') . '/';
        $jsonFile = $dir . $env . '.json';
        if (!file_exists($jsonFile)) {
            return;
        }
        $json = @json_decode(file_get_contents($jsonFile), true);
        foreach ($json as $f => $path) {
            $content = file_get_contents($dir . $env . '/' . $f);
            @file_put_contents(base_path($path), $content);
            $this->info('Hotfix: ' . $path);
        }
        $this->info("");
    }

}
