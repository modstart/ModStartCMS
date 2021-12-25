@extends('module::CmsThemeCorp.View.pc.cms.frame')

@section('pageTitleMain'){{$record['seoTitle']?$record['seoTitle']:$record['title']}}@endsection
@section('pageKeywords'){{$record['seoKeywords']?$record['seoKeywords']:$record['title']}}@endsection
@section('pageDescription'){{$record['seoDescription']?$record['seoDescription']:$record['title']}}@endsection

{!! \ModStart\ModStart::js('asset/common/lazyLoad.js') !!}
@section('bodyContent')

    <div class="lg:tw-text-left tw-text-center tw-text-white tw-text-lg tw-py-20 tw-bg-gray-500 ub-cover"
         @if($cat['bannerBg'])
         style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($cat['bannerBg'])}});"
            @endif
    >
        <div class="ub-container">
            <h1 class="tw-text-4xl">{{$cat['title']}}</h1>
            <div class="tw-mt-4">
                {{$record['summary']}}
            </div>
        </div>
    </div>

    <div class="ub-container">
        <div class="ub-breadcrumb">
            <a href="{{modstart_web_url('')}}">首页</a>
            @foreach($catChain as $i=>$c)
                <a href="{{modstart_web_url($c['url'])}}">{{$c['title']}}</a>
            @endforeach
            <a href="javascript:;" class="active">{{$record['title']}}</a>
        </div>
    </div>

    <div class="ub-container">

        <div class="ub-article-a">
            <div class="row">
                <div class="col-md-4">
                    <div class="image">
                        <div class="cover ub-cover-1-1"
                             style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($record['cover'])}});"></div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="content">
                        <h1>{{$record['title']}}</h1>
                        <div class="info">
                            <div class="ub-pair">
                                <div class="name">价格：</div>
                                <div class="value">{{$record['_data']['price'] or '暂无'}}</div>
                            </div>
                            <div class="ub-pair">
                                <div class="name">分类：</div>
                                <div class="value">{{$record['_data']['type'] or '暂无'}}</div>
                            </div>
                            <div class="ub-pair">
                                <div class="name">说明：</div>
                                <div class="value">{{$record['summary']?$record['summary']:'[摘要]'}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row margin-top">
            <div class="col-md-9">
                <div class="tw-bg-white tw-rounded">
                    <div class="ub-html" style="font-size:0.8rem;padding:1rem;">
                        {!! \ModStart\Core\Util\HtmlUtil::replaceImageSrcToLazyLoad($record['_data']['content'],'data-src',true) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">

                <div class="ub-menu simple">
                    <a class="title @if($catRoot['url']==\ModStart\Core\Input\Request::path()) active @endif"
                       href="{{modstart_web_url($catRoot['url'])}}">{{$catRoot['title']}}</a>
                    @foreach($catRootChildren as $c)
                        <a class="title @if(\ModStart\Core\Input\Request::path()==$c['url']) active @endif"
                           href="{{modstart_web_url($c['url'])}}">{{$c['title']}}</a>
                    @endforeach
                </div>

            </div>
        </div>

    </div>

@endsection





