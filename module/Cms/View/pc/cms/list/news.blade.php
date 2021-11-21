@extends($_viewFrame)

@section('pageTitleMain'){{$cat['seoTitle']?$cat['seoTitle']:$cat['title']}}@endsection
@section('pageKeywords'){{$cat['seoKeywords']?$cat['seoKeywords']:$cat['title']}}@endsection
@section('pageDescription'){{$cat['seoDescription']?$cat['seoDescription']:$cat['title']}}@endsection

@section('bodyContent')

    <div class="tw-text-white tw-text-lg tw-py-20 tw-bg-transparent ub-cover"
         style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fixFullOrDefault($cat['bannerBg'],'vendor/Cms/bg/news.jpg')}});">
        <div class="ub-container">
            <h1 class="tw-text-4xl">{{$cat['title']}}</h1>
            <div class="tw-mt-4">
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
                                                @if(!empty($record['tags']))
                                                    <i><i class="iconfont icon-tag"></i></i>
                                                    @foreach($record['tags'] as $t)
                                                        <span class="ub-tag sm">{{$t}}</span>
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





