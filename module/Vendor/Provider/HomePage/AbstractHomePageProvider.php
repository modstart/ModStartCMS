<?php


namespace Module\Vendor\Provider\HomePage;


abstract class AbstractHomePageProvider
{
    const TYPE_PC = 'pc';
    const TYPE_MOBILE = 'mobile';

    /**
     * 适配的类型
     * @return string|array
     */
    public function type()
    {
        return self::TYPE_PC;
    }

    abstract public function title();

    abstract public function action();
}
