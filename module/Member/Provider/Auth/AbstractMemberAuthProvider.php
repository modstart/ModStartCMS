<?php


namespace Module\Member\Provider\Auth;


abstract class AbstractMemberAuthProvider
{
    abstract public function name();

    abstract public function title();

    public function enabled()
    {
        return true;
    }

    public function onWebLogin($param = [])
    {
        return null;
    }

    public function onWebLogout($param = [])
    {
        return null;
    }
}
