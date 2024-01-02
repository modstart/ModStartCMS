<?php


namespace Module\Vendor\Provider\LBS;


class AddressParseResponse
{
    public static $keys = [
        'idNumber',
        'phone',
        'postcode',
        'name',
        'province',
        'city',
        'district',
        'detail',
    ];

    public static function fromArray($data)
    {
        $ret = new static();
        foreach (self::$keys as $key) {
            $ret->{$key} = isset($data[$key]) ? $data[$key] : null;
        }
        return $ret;
    }

    public $idNumber;
    public $phone;
    public $postcode;
    public $name;
    public $province;
    public $city;
    public $district;
    public $detail;
}
