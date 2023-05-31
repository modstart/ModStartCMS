<?php

namespace ModStart\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use ModStart\Core\Util\FileUtil;

class ModuleRefreshAllCommand extends Command
{
    protected $signature = 'modstart:module-refresh-all';

    public function handle()
    {
        $this->info("ModuleRefreshAll Start");

        $this->info(">>> CleanOldAsset");
        FileUtil::rm(public_path('asset'));
        $this->info(">>> Finished\n");

        $this->info(">>> Publish Asset");
        $exitCode = Artisan::call('vendor:publish', [
            '--provider' => 'ModStart\ModStartServiceProvider',
        ]);
        $output = trim(Artisan::output());
        $this->info("ExitCode -> " . $exitCode);
        $this->info($output);
        $this->info(">>> Finished\n");

        $this->info(">>> Reinstall Modules");
        $exitCode = Artisan::call('modstart:module-install-all');
        $output = trim(Artisan::output());
        $this->info("ExitCode -> " . $exitCode);
        $this->info($output);
        $this->info(">>> Finished\n");

        $this->warn("ModuleRefreshAll Finished");
    }

}
