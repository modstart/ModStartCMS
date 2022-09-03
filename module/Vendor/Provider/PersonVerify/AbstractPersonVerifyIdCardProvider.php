<?php


namespace Module\Vendor\Provider\PersonVerify;


abstract class AbstractPersonVerifyIdCardProvider
{
    abstract public function name();

    abstract public function title();

    /**
     * @param $name string
     * @param $idCardNumber string
     * @return PersonVerifyIdCardResponse
     */
    abstract public function verify($name, $idCardNumber, $param = []);

}
