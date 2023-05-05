<?php


namespace Module\Banner\View;


use Illuminate\Support\Facades\View;

class BannerView
{
    public static function basic($position,
                                 $size = '1400x400',
                                 $ratio = '5-2',
                                 $mobileRatio = '3-2',
                                 $round = false,
                                 $container = false)
    {
        if (null === $size) {
            $size = '1400x400';
        }
        if (null === $ratio) {
            $ratio = '5-2';
        }
        if (null === $mobileRatio) {
            $mobileRatio = '1-1';
        }
        if (null === $round) {
            $round = false;
        }
        return View::make('module::Banner.View.inc.banner', [
            'position' => $position,
            'bannerSize' => $size,
            'bannerRatio' => $ratio,
            'mobileBannerRatio' => $mobileRatio,
            'round' => $round,
            'container' => $container,
        ])->render();
    }
}
