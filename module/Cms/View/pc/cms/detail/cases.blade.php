@extends($_viewFrame)

@section('pageTitleMain'){{$record['seoTitle']?$record['seoTitle']:$record['title']}}@endsection
@section('pageKeywords'){{$record['seoKeywords']?$record['seoKeywords']:$record['title']}}@endsection
@section('pageDescription'){{$record['seoDescription']?$record['seoDescription']:$record['title']}}@endsection

{!! \ModStart\ModStart::js('asset/common/lazyLoad.js') !!}
@section('bodyContent')

    <div class="ub-content">
        <div class="panel-b">
            <div class="bg" style="background-image:url({{$record['cover']}});"></div>
            <div class="mask"></div>
            <div class="box">
                <div class="c">
                    <div class="c1">
                        <div class="ub-cover-4-3 tw-rounded"
                             style="background-image:url({{$record['cover']}});"></div>
                    </div>
                    <div class="c2">
                        <h1 class="title animated fadeInUp">
                            {{$record['title']}}
                        </h1>
                        <div class="sub-title animated fadeInUp">
                            {{$record['summary']}}
                        </div>
                    </div>
                </div>
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

    <div class="ub-container margin-bottom">
        @if(!\MCms::canVisitCat($cat))
            <div class="ub-alert danger">
                <i class="iconfont icon-warning"></i>
                您没有权限访问该栏目内容
            </div>
        @else
            <div class="tw-bg-white tw-rounded tw-py-10">
                <div class="ub-article">
                    <h1 class="ub-text-center">{{$record['title']}}</h1>
                    <div class="tw-p-10">
                        <div>
                            <img style="max-width:100%;" src="{{\ModStart\Core\Assets\AssetsUtil::fix($record['cover'])}}" />
                        </div>
                    </div>
                    <div class="content">
                        <div class="ub-html lg" style="padding:1rem;">
                            {!! \ModStart\Core\Util\HtmlUtil::replaceImageSrcToLazyLoad($record['_data']['content'],'data-src',true) !!}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="ub-container">
        <div class="margin-bottom ub-content-bg tw-rounded-lg tw-p-3">
            <div class="row">
                <div class="col-6">
                    <div>
                        <div class="tw-text-gray-400 tw-text-sm">上一篇</div>
                        <div class="tw-pt-2">
                            @if($recordPrev)
                                <a href="{{$recordPrev['_url']}}" class="tw-text-gray-800 tw-inline-block">
                                    {{$recordPrev['title']}}
                                </a>
                            @else
                                <span class="ub-text-muted">没有了</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="tw-text-right">
                        <div class="tw-text-gray-400 tw-text-sm">下一篇</div>
                        <div class="tw-pt-2">
                            @if($recordNext)
                                <a href="{{$recordNext['_url']}}" class="tw-text-gray-800 tw-inline-block">
                                    {{$recordNext['title']}}
                                </a>
                            @else
                                <span class="ub-text-muted">没有了</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection





