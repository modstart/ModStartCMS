<?php

namespace ModStart\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;
use ModStart\Core\Events\ModuleDisabledEvent;
use ModStart\Core\Exception\BizException;
use ModStart\ModStart;
use ModStart\Module\ModuleManager;

class ModuleDisableCommand extends Command
{
    protected $signature = 'modstart:module-disable {module}';

    public function handle()
    {
        $module = $this->argument('module');
        BizException::throwsIf(L('Module Invalid'), !ModuleManager::isExists($module));
        $installeds = ModuleManager::listAllInstalledModules();
        $basic = ModuleManager::getModuleBasic($module);
        BizException::throwsIf('Module basic empty', !$basic);

        if (method_exists(ModuleManager::class, 'callHook')) {
            ModuleManager::callHook($module, 'hookBeforeDisable');
        }

        $installeds[$module]['enable'] = false;
        ModuleManager::saveUserInstalledModules($installeds);
        ModStart::clearCache();
        $event = new ModuleDisabledEvent();
        $event->name = $module;
        Event::fire($event);
        $this->info('Module Disable Success');
    }

}
