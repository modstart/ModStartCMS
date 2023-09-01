@extends($_viewFrame)

@section('pageTitleMain'){{$record['seoTitle']?$record['seoTitle']:$record['title']}}@endsection
@section('pageKeywords'){{$record['seoKeywords']?$record['seoKeywords']:$record['title']}}@endsection
@section('pageDescription'){{$record['seoDescription']?$record['seoDescription']:$record['title']}}@endsection

{!! \ModStart\ModStart::js('asset/common/lazyLoad.js') !!}
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
                    {{$record['title']}}
                </h1>
                <div class="sub-title animated fadeInUp">
                    {{$record['summary']}}
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

    <div class="ub-container">

        <div class="row">
            <div class="col-md-9">

                <div class="ub-panel" style="padding:1rem;">
                    @if(!\MCms::canVisitCat($cat))
                        <div class="ub-alert danger">
                            <i class="iconfont icon-warning"></i>
                            您没有权限访问该栏目内容
                        </div>
                    @else
                        <div class="ub-article">
                            <h1>{{$record['title']}}</h1>
                            <div class="attr">
                                <div class="tw-flex tw-items-center">
                                    <div class="tw-flex-grow">
                                        <i class="iconfont icon-eye"></i>
                                        {{$record['viewCount']?$record['viewCount']:'-'}}
                                        &nbsp;&nbsp;
                                        <i class="iconfont icon-time"></i>
                                        {{($record['postTime'])}}
                                        &nbsp;&nbsp;
                                        @foreach($record['_tags'] as $tag)
                                            <i class="iconfont icon-tag"></i>
                                            <a class="tw-bg-gray-100 tw-leading-6 tw-inline-block tw-px-3 tw-rounded-2xl tw-text-gray-800 tw-mr-2 tw-mb-2"
                                               href="{{modstart_web_url('tag/'.urlencode($tag))}}">
                                                {{$tag}}
                                            </a>
                                        @endforeach
                                    </div>
                                    <div>
                                        @if(modstart_config('Cms_LikeAnonymityEnable',false))
                                            {!! \Module\Cms\View\CmsView::likeBtn($record['id'],['count'=>$record['likeCount']]) !!}
                                        @endif
                                    </div>
                                </div>
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
                            <div class="content ub-html lg">
                                {!! \ModStart\Core\Util\HtmlUtil::replaceImageSrcToLazyLoad($record['_data']['content'],'data-src',true) !!}
                            </div>
                        </div>
                    @endif
                </div>

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
            <div class="col-md-3">

                <div class="ub-panel">
                    <div class="head">
                        <div class="title">
                            最新发布
                        </div>
                    </div>
                    <div class="body ub-list-items">
                        @foreach(\MCms::latestContentByCat($cat['id']) as $a)
                            <a class="item-c" href="{{$a['_url']}}">{{$a['title']}}</a>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection





