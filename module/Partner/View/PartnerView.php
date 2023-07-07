<?php


namespace Module\Partner\View;


use Illuminate\Support\Facades\View;
use ModStart\ModStart;
use Module\Partner\Util\PartnerUtil;

class PartnerView
{
    public static function text($position, $param = [])
    {
        $records = PartnerUtil::listByPositionWithCache($position);
        $linkDisable = modstart_config('Partner_LinkDisable', false);
        return View::make('module::Partner.View.inc.text', array_merge($param, [
            'records' => $records,
            'position' => $position,
            'linkDisable' => $linkDisable,
        ]))->render();
    }

    public static function simple($position, $param = [])
    {
        ModStart::js('asset/common/lazyLoad.js');
        $linkDisable = modstart_config('Partner_LinkDisable', false);
        $records = PartnerUtil::listByPositionWithCache($position);
        return View::make('module::Partner.View.inc.simple', array_merge($param, [
            'records' => $records,
            'position' => $position,
            'linkDisable' => $linkDisable,
        ]))->render();
    }

    public static function mini($position, $param = [])
    {
        ModStart::js('asset/common/lazyLoad.js');
        $records = PartnerUtil::listByPositionWithCache($position);
        $linkDisable = modstart_config('Partner_LinkDisable', false);
        return View::make('module::Partner.View.inc.mini', array_merge($param, [
            'records' => $records,
            'linkDisable' => $linkDisable,
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
