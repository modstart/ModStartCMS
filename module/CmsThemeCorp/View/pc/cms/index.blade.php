@extends('module::CmsThemeCorp.View.pc.cms.frame')

@section('bodyContent')

    <div class="page-banner home-banner mb-5">
        <div class="slider-wrapper">
            <div class="owl-carousel hero-carousel">
                @foreach(\MBanner::all('home') as $banner)
                    @if($banner['type']!==\Module\Banner\Type\BannerType::VIDEO)
                        <div class="hero-carousel-item">
                            <img src="{{\ModStart\Core\Assets\AssetsUtil::fix($banner['image'])}}"/>
                            @if($banner['type']==\Module\Banner\Type\BannerType::IMAGE_TITLE_SLOGAN_LINK)
                                <div class="img-caption">
                                    <div class="subhead">{{$banner['title']}}</div>
                                    <h1 class="mb-4">{{$banner['slogan']}}</h1>
                                    <a href="{{$banner['link']}}"
                                       class="btn btn-primary">{{$banner['linkText']}}</a>
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        </div> <!-- .slider-wrapper -->
    </div> <!-- .page-banner -->

    <main>
        <div class="page-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 py-3">
                        <div class="subhead">关于我们</div>
                        <h2 class="title-section">{{modstart_config('Cms_HomeInfoTitle','[首页介绍标题]')}}</h2>
                        {!! modstart_config('Cms_HomeInfoContent','[首页公司介绍]') !!}
                        <a href="{{modstart_config('CmsThemeCorp_HomeInfoLink','/')}}" class="btn btn-primary mt-4">{{modstart_config('CmsThemeCorp_HomeInfoLinkText','查看更多')}}</a>
                    </div>
                    <div class="col-lg-6 py-3">
                        <div class="about-img">
                            <img src="{{\ModStart\Core\Assets\AssetsUtil::fixOrDefault(modstart_config('Cms_HomeInfoImage'),'vendor/CmsThemeCorp/img/about.jpg')}}" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- .page-section -->

        <div class="page-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 py-3">
                        <div class="subhead">RECOMMENDS PRODUCT</div>
                        <h2 class="title-section">产品展示</h2>
                    </div>
                    <div class="col-md-6 py-3 text-md-right">
                        <a href="{{modstart_web_url('product')}}" class="btn btn-outline-primary">查看更多
                            <span class="mai-arrow-forward ml-2"></span>
                        </a>
                    </div>
                </div>
                <div class="ub-list-items">
                    <div class="row">
                        @foreach(\MCms::paginateCatByUrl('product',1,4,['where'=>['isRecommend'=>true]]) as $record)
                            <div class="col-md-3 col-6">
                                <div class="item-p">
                                    <a class="image" href="{{$record['_url']}}" style="padding:0.25rem 0 0 0;">
                                        <div class="cover contain ub-cover-1-1"
                                             style="width:90%;margin:0 auto;background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($record['cover'])}});"></div>
                                    </a>
                                    <a class="title" href="{{$record['_url']}}">{{$record['title']}}</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="page-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 py-3">
                        <div class="subhead">RECOMMENDS CASES</div>
                        <h2 class="title-section">推荐案例</h2>
                    </div>
                    <div class="col-md-6 py-3 text-md-right">
                        <a href="{{modstart_web_url('cases')}}" class="btn btn-outline-primary">查看更多
                            <span class="mai-arrow-forward ml-2"></span>
                        </a>
                    </div>
                </div>
                <div class="row mt-3">
                    @foreach(\MCms::paginateCatByUrl('cases',1,6,['where'=>['isRecommend'=>true]]) as $record)
                        <div class="col-lg-4 py-3">
                            <div class="portfolio">
                                <a href="{{\ModStart\Core\Assets\AssetsUtil::fix($record['cover'])}}"
                                   data-fancybox="portfolio">
                                    <img src="{{\ModStart\Core\Assets\AssetsUtil::fix($record['cover'])}}" alt="{{$record['title']}}">
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div> <!-- .container -->
        </div> <!-- .page-section -->

        <div class="page-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 py-3">
                        <div class="subhead">RECOMMENDS NEWS</div>
                        <h2 class="title-section">新闻中心</h2>
                    </div>
                    <div class="col-md-6 py-3 text-md-right">
                        <a href="{{modstart_web_url('cases')}}" class="btn btn-outline-primary">查看更多
                            <span class="mai-arrow-forward ml-2"></span>
                        </a>
                    </div>
                </div>
                <div class="ub-list-items">
                    <div class="row">
                        @foreach(\MCms::paginateCatByUrl('news',1,4,['where'=>['isRecommend'=>true]]) as $record)
                            <div class="col-md-6">
                                <div class="item-k tw-bg-white margin-bottom">
                                    <a class="image" href="{{$record['_url']}}">
                                        <div class="cover ub-cover-4-3"
                                             style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($record['cover'])}})"></div>
                                    </a>
                                    <a class="title" href="{{$record['_url']}}">{{$record['title']}}</a>
                                    <div class="summary">
                                        {{\ModStart\Core\Util\HtmlUtil::text($record['summary'],200)}}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="page-section">
            <div class="container-fluid">
                <div class="row row-cols-md-3 row-cols-lg-6 justify-content-center text-center">
                    @foreach(\MPartner::all('home') as $partner)
                        <div class="py-3 px-2">
                            <img class="tw-h-10" style="filter:grayscale(100%);" src="{{\ModStart\Core\Assets\AssetsUtil::fix($partner['logo'])}}" />
                        </div>
                    @endforeach
                </div>
            </div> <!-- .container-fluid -->
        </div> <!-- .page-section -->

    </main>




@endsection
