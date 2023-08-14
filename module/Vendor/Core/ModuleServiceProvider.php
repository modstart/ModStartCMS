<?php

namespace Module\Vendor\Core;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Module\Vendor\Command\ScheduleRunnerCommand;

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
        ]);
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
