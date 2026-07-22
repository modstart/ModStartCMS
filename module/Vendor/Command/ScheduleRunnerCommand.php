<?php


namespace Module\Vendor\Command;

use Illuminate\Console\Command;
use Module\Vendor\Provider\Schedule\ScheduleBiz;
use Module\Vendor\Provider\Schedule\ScheduleProvider;

class ScheduleRunnerCommand extends Command
{
    protected $signature = 'modstart:schedule-runner {name?}';

    public function handle()
    {
        $name = $this->argument('name');
        if (empty($name)) {
            $this->line('Available schedules:');
            foreach (ScheduleBiz::all() as $provider) {
                $this->line(sprintf('  %-40s %-20s %s', $provider->name(), $provider->cron(), $provider->title()));
            }
            return;
        }
        ScheduleProvider::callByName($name);
    }
}
