<?php


namespace Module\Vendor\Type;


use ModStart\Core\Type\BaseType;

class JobStatus implements BaseType
{
    const QUEUE = 1;
    const PROCESS = 2;
    const SUCCESS = 3;
    const FAIL = 4;

    public static function getList()
    {
        return [
            self::QUEUE => '队列中',
            self::PROCESS => '处理中',
            self::SUCCESS => '成功',
            self::FAIL => '失败',
        ];
    }

}
