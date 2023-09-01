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

    <div class="ub-container">

        <div class="row">
            <div class="col-md-9">

                <div class="ub-panel" style="padding:1rem;">
                    <div class="ub-article">
                        <h1>{{$record['title']}}</h1>
                        <div class="attr">
                            <div class="tw-flex tw-items-center">
                                <i class="iconfont icon-eye"></i>
                                {{$record['viewCount']?$record['viewCount']:'-'}}
                                &nbsp;&nbsp;
                                <i class="iconfont icon-time"></i>
                                {{($record['postTime'])}}
                            </div>
                            <div>
                                @if(modstart_config('Cms_LikeAnonymityEnable',false))
                                    {!! \Module\Cms\View\CmsView::likeBtn($record['id'],['count'=>$record['likeCount']]) !!}
                                @endif
                            </div>
                        </div>
                        @if(!\MCms::canVisitCat($cat))
                            <div class="ub-alert danger">
                                <i class="iconfont icon-warning"></i>
                                您没有权限访问该栏目
                            </div>
                        @else
                            <div class="content ub-html lg">
                                {!! \ModStart\Core\Util\HtmlUtil::replaceImageSrcToLazyLoad($record['_data']['content'],'data-src',true) !!}
                            </div>
                        @endif
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





