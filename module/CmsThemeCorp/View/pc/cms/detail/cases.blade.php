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

    <div style="max-width:800px;margin:0 auto;">
        <div class="ub-container">
            <div class="ub-breadcrumb">
                <a href="{{modstart_web_url('')}}">首页</a>
                @foreach($catChain as $i=>$c)
                    <a href="{{modstart_web_url($c['url'])}}">{{$c['title']}}</a>
                @endforeach
                <a href="javascript:;" class="active">{{$record['title']}}</a>
            </div>
        </div>
        <div class="ub-container tw-bg-white tw-rounded tw-py-10">
            <div class="ub-article">
                <h1 class="ub-text-center">{{$record['title']}}</h1>
                <div class="tw-p-10">
                    <div>
                        <img style="max-width:100%;" src="{{\ModStart\Core\Assets\AssetsUtil::fix($record['cover'])}}" />
                    </div>
                </div>
                <div class="content ub-html" style="padding:1rem;font-size:0.8rem;">
                    {!! \ModStart\Core\Util\HtmlUtil::replaceImageSrcToLazyLoad($record['_data']['content'],'data-src',true) !!}
                </div>
            </div>
        </div>
    </div>

@endsection





