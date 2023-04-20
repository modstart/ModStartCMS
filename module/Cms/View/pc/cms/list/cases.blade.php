@extends($_viewFrame)

@section('pageTitleMain'){{$cat['seoTitle']?$cat['seoTitle']:$cat['title']}}@endsection
@section('pageKeywords'){{$cat['seoKeywords']?$cat['seoKeywords']:$cat['title']}}@endsection
@section('pageDescription'){{$cat['seoDescription']?$cat['seoDescription']:$cat['title']}}@endsection

@section('bodyContent')

    <div class="ub-content">
        <div class="panel-a"
             @if($cat['bannerBg'])
             style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($cat['bannerBg'])}});"
             @else
             style="background-image:var(--color-primary-gradient-bg);"
            @endif
        >
            <div class="box">
                <h1 class="title animated fadeInUp">
                    {{$cat['title']}}
                </h1>
                <div class="sub-title animated fadeInUp">
                    {{$cat['subTitle']}}
                </div>
            </div>
        </div>
    </div>

    <div class="ub-container">
        <div class="ub-breadcrumb">
            <a href="{{modstart_web_url('')}}">首页</a>
            @foreach($catChain as $i=>$c)
                <a class="@if(count($catChain)==$i+1) active @endif" href="{{modstart_web_url($c['url'])}}">{{$c['title']}}</a>
            @endforeach
        </div>
    </div>

    <div class="ub-container">

        <div class="ub-nav">
            <a class="item @if($catRoot['url']==\ModStart\Core\Input\Request::path()) active @endif" href="{{modstart_web_url($catRoot['url'])}}">全部</a>
            @foreach($catRootChildren as $c)
                <a class="item @if(\ModStart\Core\Input\Request::path()==$c['url']) active @endif" href="{{modstart_web_url($c['url'])}}">{{$c['title']}}</a>
            @endforeach
        </div>

        <div class="tw-rounded margin-top">
            @if(empty($records))
                <div class="ub-empty tw-my-20">
                    <div class="icon">
                        <div class="iconfont icon-empty-box"></div>
                    </div>
                    <div class="text">暂无记录</div>
                </div>
            @else
                <div class="ub-list-items">
                    <div class="row">
                        @foreach($records as $record)
                            <div class="col-md-3 col-6">
                                <div class="item-p">
                                    <a class="image" href="{{$record['_url']}}">
                                        <div class="cover ub-cover-1-1" style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($record['cover'])}});"></div>
                                    </a>
                                    <a class="title" href="{{$record['_url']}}">{{$record['title']}}</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="ub-page">
                    {!! $pageHtml !!}
                </div>
            @endif
        </div>

    </div>

@endsection





