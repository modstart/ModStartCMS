<?php


namespace Module\Partner\View;


use Illuminate\Support\Facades\View;
use Module\Partner\Util\PartnerUtil;

class PartnerView
{
    public static function text($position, $param = [])
    {
        $records = PartnerUtil::listByPositionWithCache($position);
        return View::make('module::Partner.View.inc.text', array_merge($param, [
            'records' => $records,
            'position' => $position,
        ]))->render();
    }

    public static function simple($position, $param = [])
    {
        $records = PartnerUtil::listByPositionWithCache($position);
        return View::make('module::Partner.View.inc.simple', array_merge($param, [
            'records' => $records,
            'position' => $position,
        ]))->render();
    }

    public static function transparent($position, $param = [])
    {
        $records = PartnerUtil::listByPositionWithCache($position);
        return View::make('module::Partner.View.inc.transparent', array_merge($param, [
            'records' => $records,
            'position' => $position,
        ]))->render();
    }

    public static function raw($position, $param = [])
    {
        $records = PartnerUtil::listByPositionWithCache($position);
        return View::make('module::Partner.View.inc.raw', array_merge($param, [
            'records' => $records,
            'position' => $position,
        ]))->render();
    }
}
