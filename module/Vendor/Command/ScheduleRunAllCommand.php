<?php


namespace Module\Vendor\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Core\Util\FileUtil;
use ModStart\Core\Util\ShellUtil;
use ModStart\Core\Util\TimeUtil;
use ModStart\ModStart;
use Module\Vendor\Util\CacheUtil;

class ScheduleRunAllCommand extends Command
{
    protected $signature = 'modstart:schedule-run-all {php} {dir}';

    public function handle()
    {
        $php = $this->argument('php');
        $dir = $this->argument('dir');
        $hash = md5($php . ':' . $dir);
        $projects = CacheUtil::remember("Vendor:ScheduleRunAll:Projects:" . $hash,
            3600,
            function () use ($dir) {
                $projects = FileUtil::listFiles($dir);
                $projects = array_filter($projects, function ($p) {
                    return $p['isDir']
                        && @file_exists($p['pathname'] . '/artisan')
                        && @file_exists($p['pathname'] . '/.env')
                        && !Str::startsWith($p['filename'], '_delete.')
                        && @file_exists($p['pathname'] . '/vendor/modstart/modstart-' . ModStart::env());
                });
                shuffle($projects);
                return ArrayUtil::keepItemsKeys($projects, ['pathname']);
            });
        foreach ($projects as $project) {
            $start = TimeUtil::millitime();
            $command = "$php {$project['pathname']}/artisan schedule:run";
            Log::info("Vendor.ScheduleRunAllCommand.Run - {$command}");
            $result = ShellUtil::run($command, false);
            $result = str_replace([
                "\r"
            ], "", $result);
            $result = str_replace("\n", " ", $result);
            $result = str_replace("Running scheduled command: Closure", "√", $result);
            $result = str_replace("No scheduled commands are ready to run.", "〇", $result);
            $ms = TimeUtil::millitime() - $start;
            Log::info("Vendor.ScheduleRunAllCommand.Result - {$result} - {$ms}ms");
        }
    }
}
