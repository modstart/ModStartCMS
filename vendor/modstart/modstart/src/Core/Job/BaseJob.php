<?php

namespace ModStart\Core\Job;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Actually, SelfHandling is deprecated in laravel 5.4. It's now default so you do not need use Illuminate\Contracts\Bus\SelfHandling;
 * and make sure to remove this implements SelfHandling. It should work then.
 */
if (PHP_VERSION_ID >= 80000) {
    abstract class BaseJob implements ShouldQueue
    {
        use Queueable, InteractsWithQueue, SerializesModels;

        /**
         * public function handle(){}
         */
    }
} else {
    abstract class BaseJob implements \Illuminate\Contracts\Bus\SelfHandling, ShouldQueue
    {
        use Queueable, InteractsWithQueue, SerializesModels;

        /**
         * public function handle(){}
         */
    }
}
