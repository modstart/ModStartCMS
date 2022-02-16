<?php

namespace ModStart\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;
use ModStart\Core\Events\ModuleEnabledEvent;
use ModStart\Core\Exception\BizException;
use ModStart\ModStart;
use ModStart\Module\ModuleManager;

class ModuleEnableCommand extends Command
{
    protected $signature = 'modstart:module-enable {module}';

    public function handle()
    {
        $module = $this->argument('module');
        BizException::throwsIf(L('Module Invalid'), !ModuleManager::isExists($module));
        $installeds = ModuleManager::listAllInstalledModules();
        $basic = ModuleManager::getModuleBasic($module);
        BizException::throwsIf('Module basic empty', !$basic);
        $installeds[$module]['enable'] = true;
        ModuleManager::saveUserInstalledModules($installeds);
        ModStart::clearCache();

        ModuleManager::callHook($module, 'hookEnabled');

        $event = new ModuleEnabledEvent();
        $event->name = $module;
        if (PHP_VERSION_ID >= 80000) {
            Event::dispatch($event);
        } else {
            Event::fire($event);
        }
        $this->info('Module Enable Success');
    }

}
