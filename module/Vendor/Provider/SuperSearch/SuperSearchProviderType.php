<?php


namespace Module\Vendor\Provider\SuperSearch;


use ModStart\Core\Type\BaseType;

class SuperSearchProviderType implements BaseType
{
    public static function getList()
    {
        return array_merge([
            '' => '默认',
        ], SuperSearchProvider::allMap());
    }

}
