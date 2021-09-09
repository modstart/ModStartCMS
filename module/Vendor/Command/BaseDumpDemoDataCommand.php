<?php


namespace Module\Vendor\Command;

use Illuminate\Console\Command;
use ModStart\Core\Dao\ModelUtil;

abstract class BaseDumpDemoDataCommand extends Command
{
    protected $signature = 'dump-demo-data';

    protected function buildInsert()
    {
        $inserts = [];
        $tables = func_get_args();
        foreach ($tables as $table) {
            if (is_array($table)) {
                $inserts[$table[0]] = ModelUtil::all($table[0], [], $table[1]);
                $ignoreCallback = isset($table[2]) ? $table[2] : null;
                if ($ignoreCallback) {
                    $inserts[$table[0]] = array_filter($inserts[$table[0]], $ignoreCallback);
                }
            } else {
                $inserts[$table] = ModelUtil::all($table);
            }
        }
        return $inserts;
    }

    protected function buildUpdate()
    {
        $args = func_get_args();
        $updates = [];
        foreach ($args as $arg) {
            $updates[] = [
                'table' => $arg[0],
                'where' => $arg[1],
                'update' => ModelUtil::get($arg[0], $arg[1], $arg[2]),
            ];
        }
        return $updates;
    }

    protected function buildDump($data)
    {
        @mkdir(public_path('data_demo'));
        file_put_contents($file = public_path('data_demo/data.php'), '<' . '?php return ' . var_export($data, true) . ';');
        $this->info("dump success -> $file");
    }

    abstract public function handle();
}