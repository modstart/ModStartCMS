<?php

define('LARAVEL_START', microtime(true));

if (file_exists(__DIR__ . '/config.php')) {
    include __DIR__ . '/config.php';
}

require __DIR__ . '/../vendor/autoload.php';

$compiledPath = __DIR__ . '/cache/compiled.php';

if (file_exists($compiledPath)) {
    require $compiledPath;
}

