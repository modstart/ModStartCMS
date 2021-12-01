@extends($_viewFrame)

@section('pageTitleMain'){{$cat['seoTitle']?$cat['seoTitle']:$cat['title']}}@endsection
@section('pageKeywords'){{$cat['seoKeywords']?$cat['seoKeywords']:$cat['title']}}@endsection
@section('pageDescription'){{$cat['seoDescription']?$cat['seoDescription']:$cat['title']}}@endsection

@section('bodyContent')

    <div class="tw-text-white tw-text-lg tw-py-20 tw-bg-gray-500 ub-cover"
         @if($cat['bannerBg'])
         style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($cat['bannerBg'])}});"
        @endif
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
                <a class="@if(count($catChain)==$i+1) active @endif"
                   href="{{modstart_web_url($c['url'])}}">{{$c['title']}}</a>
            @endforeach
        </div>
    </div>

    <div class="ub-container">
        <div class="ub-panel">
            <div class="head">
                <div class="title">
                    {{$cat['title']}}
                </div>
            </div>
            <div class="body">
                @if(empty($records))
                    <div class="ub-empty tw-my-20">
                        <div class="icon">
                            <div class="iconfont icon-empty-box"></div>
                        </div>
                        <div class="text">暂无记录</div>
                    </div>
                @else
                    <div class="ub-list-items" style="padding:0.5rem;">
                        @foreach($records as $record)
                            <div class="item-d">
                                <a class="title" href="{{$record['_url']}}">{{$record['title']}}</a>
                                <div class="attr">
                                    <i class="iconfont icon-time"></i>
                                    {{$record['_day']}}
                                </div>
                                <div class="summary">
                                    {{$record['summary']}}
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="ub-page">
                        {!! $pageHtml !!}
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection





