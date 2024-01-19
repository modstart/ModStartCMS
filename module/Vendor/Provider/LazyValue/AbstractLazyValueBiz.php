<?php


namespace Module\Vendor\Provider\LazyValue;


use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\SerializeUtil;
use Module\Vendor\Model\LazyValue;

abstract class AbstractLazyValueBiz
{
    abstract public function name();

    abstract public function title();

    abstract public function execute($param = []);

    public function cacheSeconds()
    {
        return 1800;
    }

    public function defaultValue()
    {
        return null;
    }

}
