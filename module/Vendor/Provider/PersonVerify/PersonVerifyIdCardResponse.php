<?php


namespace Module\Vendor\Provider\PersonVerify;


class PersonVerifyIdCardResponse
{
    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';

    public $code;
    public $msg;
    public $status;

    public static function build($code, $msg, $status = null)
    {
        $instance = new static();
        $instance->code = $code;
        $instance->msg = $msg;
        $instance->status = $status;
        return $instance;
    }
}
