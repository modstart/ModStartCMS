<?php
$bannerId = \ModStart\Core\Util\IdUtil::generate('Banner');
$banners = \Module\Banner\Util\BannerUtil::listByPositionWithCache($position);
if(empty($bannerSize)){
    $bannerSize = '1400x400';
}
if(empty($bannerRatio)){
    $bannerRatio = '5-2';
}
if(empty($mobileBannerRatio)){
    $mobileBannerRatio = '';
}else{
    $mobileBannerRatio = 'm-ratio-'.$mobileBannerRatio;
}
if(!isset($round)){
    $round = false;
}
?>
{!! \ModStart\ModStart::css('asset/vendor/swiper/swiper.css') !!}
{!! \ModStart\ModStart::js('asset/vendor/swiper/swiper.js') !!}
{!! \ModStart\ModStart::css('vendor/Banner/style/banner.css') !!}
<div class="ub-banner margin-bottom ratio-{{$bannerRatio}} {{$mobileBannerRatio}}" id="{{$bannerId}}">
    <div class="swiper-container">
        <div class="swiper-wrapper">
            @if(empty($banners))
                <div class="swiper-slide {{$round?'tw-rounded':''}}" style="background-image:url('/placeholder/{{$bannerSize}}');"></div>
                <div class="swiper-slide {{$round?'tw-rounded':''}}" style="background-image:url('/placeholder/{{$bannerSize}}');"></div>
                <div class="swiper-slide {{$round?'tw-rounded':''}}" style="background-image:url('/placeholder/{{$bannerSize}}');"></div>
            @else
                @foreach($banners as $banner)
                    @if($banner['type']==\Module\Banner\Type\BannerType::IMAGE)
                        <a class="swiper-slide {{$round?'tw-rounded':''}}"
                           style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($banner['image'])}});"
                           @if($banner['link']) href="{{$banner['link']}}" target="_blank" @else href="javascript:;" @endif></a>
                    @elseif($banner['type']==\Module\Banner\Type\BannerType::IMAGE_TITLE_SLOGAN_LINK)
                        <div class="swiper-slide {{$round?'tw-rounded':''}} a" style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($banner['image'])}});">
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
                    @elseif($banner['type']==\Module\Banner\Type\BannerType::VIDEO && !\ModStart\Core\Util\AgentUtil::isMobile())
                        <a class="swiper-slide {{$round?'tw-rounded':''}} video"
                           @if($banner['link']) href="{{$banner['link']}}" target="_blank" @else href="javascript:;" @endif>
                            <video class="video-player"
                                   src="{{\ModStart\Core\Assets\AssetsUtil::fix($banner['video'])}}"
                                   autoplay="autoplay" loop="loop" muted="muted"></video>
                        </a>
                    @endif
                @endforeach
            @endif
        </div>
        @if(count($banners)>1)
            <div class="swiper-pagination swiper-pagination-white"></div>
            <div class="swiper-button-next swiper-button-white"></div>
            <div class="swiper-button-prev swiper-button-white tw-delay-200"></div>
        @endif
    </div>
</div>
@if(count($banners)>1)
    <script>
        $(function () {
            var changeAnimate = function(slide){
                var $content = $(slide).find('.content');
                if($content.length>0){
                    $content.find('.title').removeClass('animated fadeInUp');
                    $content.find('.slogan').removeClass('animated fadeInUp');
                    $content.find('.link').removeClass('animated fadeInUp');
                    setTimeout(function(){
                        $content.find('.title').addClass('animated fadeInUp');
                        $content.find('.slogan').addClass('animated fadeInUp');
                        $content.find('.link').addClass('animated fadeInUp');
                    },0);
                }
            };
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
                },
                observer: true,
                observeParents: true,
                on: {
                    observerUpdate: function(){
                        swiperRefresh();
                    },
                    resize: function () {
                        swiperRefresh();
                    }
                }
            });
            var swiperRefresh = function(){
                setTimeout(function(){
                    swiper.update();
                }, 500);
            };
            changeAnimate(swiper.slides[swiper.activeIndex]);
            swiper.on('slideChange',function(){
                changeAnimate(swiper.slides[swiper.activeIndex]);
            });
        });
    </script>
@endif

