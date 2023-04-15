<?php
$bannerId = \ModStart\Core\Util\IdUtil::generate('Banner');
if(!isset($position)){
    $position = 'home';
}
if(!isset($navPosition)){
    $navPosition = 'header';
}
if(empty($bannerSize)){
    $bannerSize = '1400x400';
}
if(empty($bannerRatio)){
    $bannerRatio = '2-1';
}
if(!isset($heightFull)){
    $heightFull = false;
}
$banners = \Module\Banner\Util\BannerUtil::listByPositionWithCache($position);
?>
{!! \ModStart\ModStart::css('vendor/Banner/style/banner.css') !!}
{!! \ModStart\ModStart::css('asset/vendor/swiper/swiper.css') !!}
{!! \ModStart\ModStart::js('asset/vendor/swiper/swiper.js') !!}

<header class="ub-header transparent absolute lg">
    <div class="ub-container">
        <div class="logo">
            <a href="{{$__msRoot}}">
                <img src="{{\ModStart\Core\Assets\AssetsUtil::fix(modstart_config('siteLogo'))}}"/>
            </a>
        </div>
        <div class="menu-mask" onclick="$('body').removeClass('ub-header-show')"></div>
        <div class="menu">
            @if(!empty($navs))
                @foreach($navs as $nav)
                    <a class="item animated fadeInUp {{modstart_baseurl_active($nav['link'])}}" href="{{$nav['link']}}" @if(!empty($nav['newBlank'])) target="_blank" @endif>{!! $nav['name'] !!}</a>
                @endforeach
            @else
                @foreach(\Module\Nav\Util\NavUtil::listByPositionWithCache($navPosition) as $nav)
                    <a class="item animated fadeInUp {{modstart_baseurl_active($nav['link'])}}" href="{{$nav['link']}}" @if(!empty($nav['newBlank'])) target="_blank" @endif>{{$nav['name']}}</a>
                @endforeach
            @endif
        </div>
        <a class="menu-toggle" href="javascript:;" onclick="MS.header.trigger()">
            <i class="show iconfont icon-list"></i>
            <i class="close iconfont icon-close"></i>
        </a>
    </div>
</header>

<?php $bannerId = \ModStart\Core\Util\IdUtil::generate('BannerNav'); ?>
@if(!empty($heightFull))
    {!! \ModStart\ModStart::style('#'.$bannerId.',#'.$bannerId.' .swiper-slide{height:100vh;} @media screen and (max-width:40rem){ #'.$bannerId.',#'.$bannerId.' .swiper-slide{height:auto;} }') !!}
@endif
<div id="{{$bannerId}}" class="ub-banner ratio-{{$bannerRatio}}">
    <div class="swiper-container">
        <div class="swiper-wrapper">
            @if(empty($banners))
                @for($i=0;$i<3;$i++)
                    <div class="swiper-slide {{$round?'tw-rounded':''}}" style="background-color:#EEE;">
                        <div class="cover" style="background-image:url('/placeholder/{{$bannerSize}}');"></div>
                    </div>
                @endfor
            @else
                @foreach($banners as $banner)
                    @if($banner['type']===\Module\Banner\Type\BannerType::IMAGE)
                        <a class="swiper-slide"
                           @if($banner['link']) href="{{$banner['link']}}" target="_blank" @else href="javascript:;" @endif>
                            <div class="cover" style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($banner['image'])}});"></div>
                        </a>
                    @elseif($banner['type']===\Module\Banner\Type\BannerType::IMAGE_TITLE_SLOGAN_LINK)
                        <div class="swiper-slide a">
                            <div class="cover" style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($banner['image'])}});">
                                <div class="content @if(!empty($banner['colorReverse'])) reverse @endif">
                                    <div class="title">{{$banner['title']}}</div>
                                    <div class="slogan">
                                        @foreach(explode("\n",trim($banner['slogan'])) as $line)
                                            <div class="line">{{$line}}</div>
                                        @endforeach
                                    </div>
                                    <a class="link" href="{{$banner['link']}}" target="_blank">{{$banner['linkText']}}</a>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>
        @if(count($banners)>1)
            <div class="swiper-pagination swiper-pagination-white"></div>
            <div class="swiper-button-next swiper-button-white"></div>
            <div class="swiper-button-prev swiper-button-white"></div>
        @endif
    </div>
</div>
@if(count($banners)>1)
    <script>
        $(function () {
            var swiper = new Swiper('#{{$bannerId}} .swiper-container', {
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                loop: true,
                autoplay: {
                    delay: 3000
                }
            });
        });
    </script>
@endif
