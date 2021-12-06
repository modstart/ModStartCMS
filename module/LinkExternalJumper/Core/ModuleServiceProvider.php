<?php

namespace Module\LinkExternalJumper\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use ModStart\Admin\Config\AdminMenu;
use ModStart\Core\Hook\ModStartHook;

class ModuleServiceProvider extends ServiceProvider
{
    
    public function boot(Dispatcher $events)
    {
        ModStartHook::subscribe('PageBodyAppend', function () {
            return View::make('module::LinkExternalJumper.View.widget.script')->render();
        });
    }

    
    public function register()
    {

    }
}
