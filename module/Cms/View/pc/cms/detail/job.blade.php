@extends($_viewFrame)

@section('pageTitleMain'){{$record['seoTitle']?$record['seoTitle']:$record['title']}}@endsection
@section('pageKeywords'){{$record['seoKeywords']?$record['seoKeywords']:$record['title']}}@endsection
@section('pageDescription'){{$record['seoDescription']?$record['seoDescription']:$record['title']}}@endsection

{!! \ModStart\ModStart::js('asset/common/lazyLoad.js') !!}
@section('bodyContent')

    <div class="tw-text-white tw-text-lg tw-py-20 tw-bg-transparent ub-cover tw-bg-fixed" style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fixFullOrDefault($cat['bannerBg'],'vendor/Cms/bg/news.jpg')}});">
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

        <div class="row">
            <div class="col-md-9">

                <div class="ub-panel" style="padding:1rem;">
                    <div class="ub-article">
                        <h1>{{$record['title']}}</h1>
                        <div class="attr">
                            <i class="iconfont icon-time"></i>
                            {{($record['postTime'])}}
                        </div>
                        <div class="tw-p-4 tw-rounded tw-bg-gray-100">
                            <div class="ub-pair">
                                <div class="name">岗位类型：</div>
                                <div class="value">
                                    {{$record['_data']['type']}}
                                </div>
                            </div>
                            <div class="ub-pair">
                                <div class="name">招聘人数：</div>
                                <div class="value">
                                    {{$record['_data']['amount']}}
                                </div>
                            </div>
                        </div>
                        <div class="content ub-html" style="font-size:0.8rem;">
                            {!! \ModStart\Core\Util\HtmlUtil::replaceImageSrcToLazyLoad($record['_data']['content'],'data-src',true) !!}
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-3">

                <div class="ub-panel">
                    <div class="head">
                        <div class="title">
                            最新发布
                        </div>
                    </div>
                    <div class="body ub-list-items">
                        @foreach($latestRecords as $a)
                            <a class="item-c" href="{{$a['_url']}}">{{$a['title']}}</a>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection





