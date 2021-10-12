<?php

namespace ModStart\Core\Dao;

use Illuminate\Database\Eloquent\Model;
use ModStart\Core\Util\FileUtil;
use ModStart\Core\Util\StubUtil;

class DynamicModel extends Model
{
    /**
     * @param $table
     * @return Model
     */
    public static function make($table)
    {
        $className = 'DynamicModel_' . $table;
        $class = '\\DynamicModel\\' . $className;
        if (!class_exists($class)) {
            $file = base_path('bootstrap/cache/' . $className . '.php');
            if (!file_exists($file)) {
                $content = StubUtil::render('DynamicModel', [
                    'className' => $className,
                    'table' => $table,
                ]);
                file_put_contents($file, $content);
            }
            require $file;
        }
        return new $class();
    }
}