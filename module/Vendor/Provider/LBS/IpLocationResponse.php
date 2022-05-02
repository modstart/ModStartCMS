<?php


namespace Module\Vendor\Provider\LBS;


class IpLocationResponse
{
    public static $keys = [
        'country',
        'province',
        'city',
        'district',
        'isp'
    ];

    public static function fromArray($data)
    {
        $ret = new static();
        foreach (self::$keys as $key) {
            $ret->{$key} = isset($data[$key]) ? $data[$key] : null;
        }
        return $ret;
    }

    public $country;
    public $province;
    public $city;
    public $district;
    public $isp;
}
