@extends($_viewFrame)

@section('pageTitleMain')搜索：{{$keywords}}@endsection
@section('pageKeywords')搜索：{{$keywords}}@endsection
@section('pageDescription')搜索：{{$keywords}}@endsection

{!! \ModStart\ModStart::js('asset/vendor/jqueryMark.js') !!}
{!! \ModStart\ModStart::style('[data-markjs]{color:red !important;background:transparent;}') !!}
{!! \ModStart\ModStart::script("$('.ub-list-items .title,.ub-list-items .tag').mark(".json_encode($keywords).".split('').join(' '),{separateWordSearch:true});") !!}

@section('bodyContent')

    <div class="ub-search-block">
        <div class="title">
            搜索
        </div>
        <div class="form">
            <form action="{{modstart_web_url('search')}}" method="get">
                <div class="box">
                    <input type="text" name="keywords" value="{{empty($keywords)?'':$keywords}}" class="form form-lg" placeholder="输入关键词搜索" />
                    <button type="submit" class="btn btn-lg"><i class="iconfont icon-search"></i> 搜索</button>
                </div>
            </form>
        </div>
    </div>

    <div class="ub-container margin-top">
        <div class="ub-search-result tw-rounded-lg">
            搜索 <span class="keyword">{{$keywords}}</span> ，共找到 <span class="count">{{$total}}</span> 条记录
        </div>
    </div>


    <div class="ub-container margin-top">
        <div class="ub-panel">
            <div class="head">
                <div class="title">
                    搜索结果
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
                                <a class="title" target="_blank" href="{{$record['_url']}}">{{$record['title']}}</a>
                                <div class="summary">
                                    {{$record['summary']}}
                                </div>
                                <div class="action">
                                    <div class="left">
                                        @if(!empty($record['_tags']))
                                            <div class="tw-mt-2 tag">
                                                @foreach($record['_tags'] as $tag)
                                                    <a class="tw-bg-gray-100 tw-leading-6 tw-inline-block tw-px-3 tw-rounded-2xl tw-text-gray-800 tw-mr-2 tw-mb-2"
                                                       href="{{modstart_web_url('tag/'.urlencode($tag))}}">
                                                        {{$tag}}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <div class="right">
                                        <i class="iconfont icon-eye"></i>
                                        {{$record['viewCount']?$record['viewCount']:'-'}}
                                        &nbsp;&nbsp;
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

@endsection





