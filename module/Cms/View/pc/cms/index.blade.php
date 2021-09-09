@extends($_viewFrame)

@section('pageTitle'){{modstart_config('siteName').' - '.modstart_config('siteSlogan')}}@endsection


{!! \ModStart\ModStart::js('asset/common/timeago.js') !!}

@section('bodyContent')

    <div class="ub-container margin-top">
        <div class="row">
            <div class="col-md-8">
                <div id="banner">
                    @include('module::Banner.View.pc.public.banner',['position'=>'home','bannerRatio'=>'5-2'])
                </div>
                @foreach($channelTree as $channelGroup)
                    @if(!empty($channelLatestPost[$channelGroup['id']]))
                        <div class="ub-panel margin-top">
                            <div class="head">
                                <div class="more">
                                    <a href="{{modstart_web_url('channel/'.$channelGroup['alias'])}}">更多</a>
                                </div>
                                <div class="title">{{$channelGroup['title']}}</div>
                            </div>
                            <div class="body ub-list-items">
                                @foreach($channelLatestPost[$channelGroup['id']] as $record)
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
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="col-md-4">
                <div class="ub-panel" id="postLatest">
                    <div class="head">
                        <div class="title">最近更新</div>
                    </div>
                    <div class="body ub-list-items" style="overflow:auto;">
                        @foreach($latestPosts as $record)
                            <a class="item-c" href="{{modstart_web_url('p/'.$record['alias'])}}">{{$record['title']}}</a>
                        @endforeach
                    </div>
                    <script>
                        $(function () {
                            $('#postLatest .body').css('height',($('#banner').height()-30)+'px');
                        });
                    </script>
                </div>
{{--                <div>--}}
{{--                    <img style="width:100%;" src="/placeholder/300x200" alt="">--}}
{{--                </div>--}}
                <div class="ub-panel margin-top">
                    <div class="head">
                        <div class="title">频道</div>
                    </div>
                    <div class="body">
                        <div class="body">
                            <div class="ub-nav-category">
                                @foreach($channelTree as $channelGroup)
                                    <a class="group-title" href="{{modstart_web_url('channel/'.$channelGroup['alias'])}}">{{$channelGroup['title']}}</a>
                                    <div class="group-list">
                                        @foreach($channelGroup['_child'] as $channel)
                                        <a class="item" href="{{modstart_web_url('channel/'.$channel['alias'])}}">{{$channel['title']}}</a>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="ub-container">
        @include('module::Partner.View.pc.public.partner',['position'=>'home'])
    </div>

@endsection
