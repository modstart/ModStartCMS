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
                   href="{{$c['_url']}}">{{$c['title']}}</a>
            @endforeach
        </div>
    </div>

    <div class="ub-container">

        <div class="row">
            <div class="col-md-3">

                <div class="ub-menu simple margin-bottom">
                    <a class="title @if($catRoot['url']==\ModStart\Core\Input\Request::path()) active @endif"
                       href="{{$catRoot['_url']}}">全部</a>
                    @foreach($catRootChildren as $c)
                        <a class="title @if(\ModStart\Core\Input\Request::path()==$c['url']) active @endif"
                           href="{{$c['_url']}}">{{$c['title']}}</a>
                    @endforeach
                </div>
            </div>
            <div class="col-md-9">
                <div class="ub-panel">
                    <div class="head">
                        <div class="title">
                            {{$cat['title']}}
                        </div>
                    </div>
                    <div class="body" style="padding:0;">
                        @if(empty($records))
                            <div class="ub-empty tw-my-20">
                                <div class="icon">
                                    <div class="iconfont icon-empty-box"></div>
                                </div>
                                <div class="text">暂无记录</div>
                            </div>
                        @else
                            <div class="ub-list-items">
                                @foreach($records as $record)
                                    <div class="item-k">
                                        <a class="image" href="{{$record['_url']}}">
                                            <div class="cover ub-cover-4-3"
                                                 style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($record['cover'])}})"></div>
                                        </a>
                                        <a class="title" href="{{$record['_url']}}">{{$record['title']}}</a>
                                        <div class="summary">
                                            {{\ModStart\Core\Util\HtmlUtil::text($record['summary'],200)}}
                                        </div>
                                        <div class="info">
                                            <div class="left">
                                                @if(!empty($record['_tags']))
                                                    <i><i class="iconfont icon-tag"></i></i>
                                                    @foreach($record['_tags'] as $t)
                                                        <a href="{{modstart_web_url('tag/'.urlencode($t))}}"
                                                           class="tw-bg-gray-100 tw-leading-6 tw-inline-block tw-px-3 tw-rounded-2xl tw-text-gray-800 tw-mr-2 tw-mb-2">{{$t}}</a>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <div class="right">
                                                <i class="iconfont icon-time"></i>
                                                {{$record['_day']}}
                                            </div>
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
        </div>

    </div>

@endsection





