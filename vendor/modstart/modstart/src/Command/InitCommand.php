<?php

namespace ModStart\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use ModStart\Admin\Auth\Admin;
use ModStart\Admin\Model\AdminUser;
use ModStart\Core\Dao\ModelUtil;

class InitCommand extends Command
{
    protected $signature = 'modstart:init {--username=} {--password=}';

    public function handle()
    {
        $username = $this->option('username');
        $password = $this->option('password');
        while (empty($username)) {
            $username = $this->ask('Please Input Username');
        }
        while (empty($password)) {
            $password = $this->ask('Please Input Password');
        }

        $this->info('ModStart.Init - migrate - start');
        $ret = Artisan::call('migrate', ['--force' => true]);
        $this->info('ModStart.Init - migrate - end - code:' . $ret);

        $this->info('ModStart.Init - modstart:module-install-all - start');
        $ret = Artisan::call('modstart:module-install-all');
        $this->info('ModStart.Init - modstart:module-install-all - end - code:' . $ret);

        if (ModelUtil::count(AdminUser::class) == 0) {
            $admin = Admin::add($username, $password);
            $this->info('ModStart.Init - init user - id:' . $admin['id'] . ', username:' . $username . ', password: ' . $password);
        } else {
            $this->info('ModStart.Init - init user - ignore');
        }

        $lockFile = storage_path('install.lock');
        if (!file_exists($lockFile)) {
            file_put_contents($lockFile, 'ok');
        }
    }

}
