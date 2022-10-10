<?php


namespace Module\Member\Provider\RegisterProcessor;


abstract class AbstractMemberRegisterProcessorProvider
{
    public function order()
    {
        return 100;
    }

    abstract public function name();

    abstract public function title();

    abstract public function render();

    abstract public function preCheck();

    abstract public function postProcess($memberUserId);
}
