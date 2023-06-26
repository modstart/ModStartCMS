@extends($_viewFrame)

@section('pageTitle'){{modstart_config('siteName').' | '.modstart_config('siteSlogan')}}@endsection

{!! \ModStart\ModStart::js('asset/common/scrollAnimate.js') !!}
@section('bodyContent')

    <div style="background:#FFF;">
        @if(\ModStart\Core\Util\AgentUtil::isMobile())
            {!! \Module\Banner\View\BannerView::basic('Cms',null,'5-3') !!}
        @else
            {!! \Module\Banner\View\BannerView::basic('Cms',null,'5-2') !!}
        @endif
    </div>

    <div class="ub-container">

        <div class="tw-bg-white tw-p-4 lg:tw-p-8 tw-rounded margin-top">
            <div class="row">
                <div class="col-md-3">
                    <img class="tw-w-full tw-rounded tw-mb-2" data-scroll-animate="animated fadeInUp"
                         src="{{\ModStart\Core\Assets\AssetsUtil::fix(modstart_config('Cms_HomeInfoImage','/placeholder/300x200'))}}"/>
                </div>
                <div class="col-md-9">
                    <div class="tw-text-lg lg:tw-text-3xl">{{modstart_config('Cms_HomeInfoTitle','[首页介绍标题]')}}</div>
                    <div class="ub-html tw-mt-4">
                        {!! modstart_config('Cms_HomeInfoContent','[首页介绍说明]') !!}
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="ub-nav-header">
                产品展示
            </div>
            <div class="tw-pb-8">
                <div class="ub-list-items">
                    <div class="row">
                        @foreach(\MCms::listContentByCatUrl('product',1,4,['where'=>['isRecommend'=>true]]) as $record)
                            <div class="col-md-3 col-6">
                                <div class="item-p" data-scroll-animate="animated fadeInUp">
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

        <div>
            <div class="ub-nav-header">
                客户案例
            </div>
            <div class="">
                <div class="ub-list-items">
                    <div class="row">
                        @foreach(\MCms::listContentByCatUrl('cases',1,4,['where'=>['isRecommend'=>true]]) as $record)
                            <div class="col-md-3 col-6">
                                <div class="item-p animated" data-scroll-animate="animated fadeInUp">
                                    <a class="image" href="{{$record['_url']}}">
                                        <div class="cover ub-cover-1-1" style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($record['cover'])}});"></div>
                                    </a>
                                    <a class="title" href="{{$record['_url']}}">{{$record['title']}}</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="ub-nav-header">
                新闻资讯
            </div>
            <div class="">
                <div class="ub-list-items">
                    <div class="row">
                        @foreach(\MCms::listContentByCatUrl('news',1,4,['where'=>['isRecommend'=>true]]) as $record)
                            <div class="col-md-6">
                                <div class="item-k tw-bg-white margin-bottom" data-scroll-animate="animated fadeInUp">
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

        <div>
            <div class="ub-nav-header">
                合作伙伴
            </div>
            <div class="" data-scroll-animate="animated fadeInUp">
                {!! \Module\Partner\View\PartnerView::raw('Cms') !!}
            </div>
        </div>

    </div>

@endsection
