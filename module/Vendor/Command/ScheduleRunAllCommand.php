<?php


namespace Module\Vendor\Command;

use Illuminate\Console\Command;
use ModStart\Core\Util\FileUtil;
use ModStart\Core\Util\ShellUtil;
use ModStart\ModStart;

class ScheduleRunAllCommand extends Command
{
    protected $signature = 'modstart:schedule-run-all {php} {dir}';

    public function handle()
    {
        $php = $this->argument('php');
        $dir = $this->argument('dir');
        $projects = FileUtil::listFiles($dir);
        $projects = array_filter($projects, function ($p) {
            return $p['isDir']
                && file_exists($p['pathname'] . '/artisan')
                && file_exists($p['pathname'] . '/vendor/modstart/modstart-' . ModStart::env());
        });
        foreach ($projects as $project) {
            $command = "$php {$project['pathname']}/artisan schedule:run";
            ShellUtil::run($command);
        }
    }
}
