<?php


namespace Module\Vendor\Command;

use Illuminate\Console\Command;
use Module\Vendor\Provider\Schedule\ScheduleProvider;

class ScheduleRunnerCommand extends Command
{
    protected $signature = 'modstart:schedule-runner {name}';

    public function handle()
    {
        $name = $this->argument('name');
        ScheduleProvider::callByName($name);
    }
}
