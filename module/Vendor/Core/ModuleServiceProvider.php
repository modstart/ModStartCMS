<?php

namespace Module\Vendor\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Module\Vendor\Command\ScheduleRunAllCommand;
use Module\Vendor\Command\ScheduleRunnerCommand;
use Module\Vendor\Provider\Schedule\ScheduleBiz;
use Module\Vendor\Schedule\DataTempCleanScheduleBiz;
use Module\Vendor\Schedule\TempFileCleanScheduleBiz;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        $this->commands([
            ScheduleRunnerCommand::class,
            ScheduleRunAllCommand::class,
        ]);
        if (class_exists(DataTempCleanScheduleBiz::class)) {
            ScheduleBiz::register(DataTempCleanScheduleBiz::class);
            ScheduleBiz::register(TempFileCleanScheduleBiz::class);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
