<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Module\Vendor\Provider\Schedule\ScheduleProvider;

class Kernel extends ConsoleKernel
{
    
    protected $commands = [
        Commands\DumpDemoDataCommand::class,
    ];

    
    protected function schedule(Schedule $schedule)
    {
        ScheduleProvider::call($schedule);
    }
}
