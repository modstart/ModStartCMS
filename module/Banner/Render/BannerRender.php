<?php


namespace Module\Banner\Render;


use Illuminate\Support\Facades\View;

class BannerRender
{
    public static function position($position,
                                    $size = '1400x400',
                                    $ratio = '5-2',
                                    $mobileRatio = '3-2',
                                    $round = false)
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
        return View::make('module::Banner.View.pc.public.banner', [
            'position' => $position,
            'bannerSize' => $size,
            'bannerRatio' => $ratio,
            'mobileBannerRatio' => $mobileRatio,
            'round' => $round,
        ])->render();
    }
}
