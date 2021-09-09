<?php
echo "\n\n";

set_time_limit(0);

$ret = shell_exec("git pull origin master 2>&1");
$lines = explode("\n", $ret);
if (isset($lines[0])) {
    unset($lines[0]);
}
echo "=== Deploy ===\n" . join("\n", $lines) . "\n";

$ret = shell_exec("php ../artisan migrate 2>&1");
echo "=== Migrate ===\n" . $ret . "\n";

$ret = shell_exec("php ../artisan modstart:module-install-all 2>&1");
echo "=== ModStart ModuleInstallAll ===\n" . $ret . "\n";

$ret = shell_exec("php ../artisan cache:clear 2>&1");
echo "=== Cache clear ===\n" . $ret . "\n";

$ret = shell_exec("php ../artisan view:clear 2>&1");
echo "=== View clear ===\n" . $ret . "\n";

$ret = shell_exec("php ../artisan config:cache 2>&1");
echo "=== Config cache ===\n" . $ret . "\n";

$ret = shell_exec("php ../artisan optimize 2>&1");
echo "=== Optimize ===\n" . $ret . "\n";

