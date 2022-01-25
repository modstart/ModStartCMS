<?php


namespace Module\Vendor\Provider\UgcCensor;


use ModStart\Core\Input\Response;

abstract class AbstractUgcCensorProvider
{
    abstract public function name();

    abstract public function title();

    abstract public function verify($content, $param = []);

    public function isVerifyPass($content, $param = [])
    {
        $ret = $this->verify($content, $param);
        if (Response::isSuccess($ret)) {
            if ($ret['data']['pass']) {
                return true;
            }
        }
        return false;
    }
}