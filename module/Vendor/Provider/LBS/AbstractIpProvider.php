<?php


namespace Module\Vendor\Provider\LBS;


abstract class AbstractIpProvider
{
    abstract public function name();

    abstract public function title();

    /**
     * @param $ip
     * @param array $param
     * @return IpLocationResponse
     */
    abstract public function getLocation($ip, $param = []);

}
