<?php


namespace Module\Member\Biz\Vip;


use ModStart\Support\Concern\HasFields;

abstract class AbstractMemberVipBiz
{
    abstract public function name();

    abstract public function title();

    /**
     * @param $builder HasFields
     */
    public function vipField($builder)
    {

    }

}
