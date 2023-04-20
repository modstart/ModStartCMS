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
                <a class="@if(count($catChain)==$i+1) active @endif"
                   href="{{modstart_web_url($c['url'])}}">{{$c['title']}}</a>
            @endforeach
        </div>
    </div>

    <div class="ub-container">

        <div class="row">
            <div class="col-md-3">

                <div class="ub-menu simple margin-bottom">
                    <a class="title @if($catRoot['url']==\ModStart\Core\Input\Request::path()) active @endif"
                       href="{{modstart_web_url($catRoot['url'])}}">全部</a>
                    @foreach($catRootChildren as $c)
                        <a class="title @if(\ModStart\Core\Input\Request::path()==$c['url']) active @endif"
                           href="{{modstart_web_url($c['url'])}}">{{$c['title']}}</a>
                    @endforeach
                </div>

            </div>
            <div class="col-md-9">
                <div class="ub-panel">
                    <div class="head">
                        <div class="title">
                            {{$c['title']}}
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
                            <div>
                                @foreach($records as $record)
                                    <a class="tw-bg-gray-100 tw-px-2 tw-py-2 ub-text-default tw-block tw-rounded tw-mb-1"
                                       href="{{$record['_url']}}">
                                        <i class="iconfont icon-dot-sm"></i>
                                        {{$record['title']}}
                                    </a>
                                @endforeach
                            </div>
                            <div class="ub-page">
                                {!! $pageHtml !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection





