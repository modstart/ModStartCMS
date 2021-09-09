@extends($_viewFrame)

@section('pageTitle'){{modstart_config('siteName').' - '.modstart_config('siteSlogan')}}@endsection


{!! \ModStart\ModStart::js('asset/common/timeago.js') !!}

@section('bodyContent')

    <div class="ub-container margin-top">
        <div class="row">
            <div class="col-md-12">
                <div class="ub-panel margin-top">
                    <div class="head">
                        <div class="title">{{$channel['title']}}</div>
                    </div>
                    <div class="body">
                        {{$channel['description']}}
                    </div>
                </div>
                <div class="ub-panel margin-top">
                    <div class="head">
                        <div class="title">文章列表</div>
                    </div>
                    <div class="body ub-list-items">
                        @foreach($records as $record)
                            <div class="item-k">
                                <a class="image" href="{{modstart_web_url('p/'.$record['alias'])}}">
                                    <div class="cover ub-cover-4-3 tw-bg-gray-200" style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($record['_cover'])}})"></div>
                                </a>
                                <a class="title" href="{{modstart_web_url('p/'.$record['alias'])}}">{{$record['title']}}</a>
                                <div class="summary">
                                    {{$record['_summary']}}
                                </div>
                                <div class="info">
                                    <div class="left">
                                        频道：
                                        @if($record['_channel'])
                                            <a href="{{modstart_web_url('channel/'.$record['_channel']['alias'])}}">{{$record['_channel']['title'] or ''}}</a>
                                        @else
                                            -
                                        @endif
                                    </div>
                                    <div class="right">
                                        <i class="iconfont icon-time"></i>
                                        <time datetime="{{$record['created_at']}}"></time>
                                        &nbsp;
                                        <i class="iconfont icon-eye"></i>
                                        {{$record['viewCount'] or 0}}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="ub-page">
                            {!! $pageHtml !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
