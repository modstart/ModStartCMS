<?php


namespace Module\Vendor\Util;


use Illuminate\Support\Facades\Session;

class MobileUtil
{
    public static function putEntryData($key, $value)
    {
        $data = Session::get('Mobile_EntryData', []);
        if (empty($data)) {
            $data = [];
        }
        $data[$key] = $value;
        Session::put('Mobile_EntryData', $data);
    }

    public static function getEntryData()
    {
        $data = Session::get('Mobile_EntryData', []);
        if (empty($data)) {
            $data = new \stdClass();
        }
        return $data;
    }
}
