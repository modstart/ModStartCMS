@extends($_viewFrame)

@section('pageTitleMain'){{$cat['seoTitle']?$cat['seoTitle']:$cat['title']}}@endsection
@section('pageKeywords'){{$cat['seoKeywords']?$cat['seoKeywords']:$cat['title']}}@endsection
@section('pageDescription'){{$cat['seoDescription']?$cat['seoDescription']:$cat['title']}}@endsection

{!! \ModStart\ModStart::js('asset/common/lazyLoad.js') !!}
@section('bodyContent')

    <div class="lg:tw-text-left tw-text-center tw-text-white tw-text-lg tw-py-20 tw-bg-gray-500 ub-cover"
         @if($cat['bannerBg']) style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($cat['bannerBg'])}});" @endif
    >
        <div class="ub-container">
            <h1 class="tw-text-4xl animated fadeInUp">{{$cat['title']}}</h1>
            <div class="tw-mt-4 animated fadeInUp">
                {{$cat['subTitle']}}
            </div>
        </div>
    </div>

    <div class="ub-container">
        <div class="ub-breadcrumb">
            <a href="{{modstart_web_url('')}}">首页</a>
            @foreach($catChain as $i=>$c)
                <a href="{{modstart_web_url($c['url'])}}">{{$c['title']}}</a>
            @endforeach
        </div>
    </div>

    <div class="ub-container">
        <div class="row">
            <div class="col-md-12">
                <div class="ub-panel" style="padding:1rem;">
                    <div class="ub-article">
                        <h1 class="ub-text-center">{{$record['title']}}</h1>
                        <div class="attr ub-text-center">
                            <i class="iconfont icon-time"></i>
                            {{($record['postTime'])}}
                        </div>
                        <div class="content ub-html lg">
                            {!! \ModStart\Core\Util\HtmlUtil::replaceImageSrcToLazyLoad($record['_data']['content'],'data-src',true) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection





