<?php


namespace App\Console\Commands;


use Illuminate\Console\Command;
use ModStart\Data\DataManager;

class MigrateJob extends Command
{
    protected $signature = 'MigrateJob';

    public function handle()
    {

    }

    private function storageTest()
    {
        $storage = DataManager::storage();
        var_dump($storage->has('aaa.txt'));
        var_dump($storage->put('aaa.txt', 'bbb'));
        var_dump($storage->get('aaa.txt'));
        var_dump($storage->size('aaa.txt'));
        var_dump($storage->delete('aaa.txt'));
        var_dump($storage->put('bbb.txt', 'ccc'));
        var_dump($storage->softDelete('bbb.txt'));
    }
}
