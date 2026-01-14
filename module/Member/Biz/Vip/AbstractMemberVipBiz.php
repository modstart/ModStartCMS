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

    public function vipFieldShow($item)
    {
        return '<div class="ub-text-muted">没有内容</div>';
    }

}
