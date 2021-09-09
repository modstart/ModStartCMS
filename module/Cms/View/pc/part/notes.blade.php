@if(empty($notes))
    <div class="ub-empty">
        <div class="icon">
            <div class="iconfont icon-empty-box"></div>
        </div>
        <div class="text">暂无记录</div>
    </div>
@else
    {!! \ModStart\ModStart::js('asset/common/timeago.js') !!}
    @foreach($notes as $note)
        <div class="tw-m-2">
            <div class="tw-bg-white tw-p-4 tw-border-0 tw-border-b tw-border-solid tw-border-gray-200">
                <a href="{{modstart_web_url('n/'.$note['alias'])}}" class="tw-block tw-truncate tw-text-black tw-text-lg">
                    {{$note['title']}}
                </a>
                <div class="tw-flex tw-mt-4">
                    <a href="{{modstart_web_url('note_member/'.$note['memberUserId'])}}" class="tw-block tw-w-4 tw-h-4 tw-bg-cover tw-bg-center tw-rounded-full tw-shadow-inner"
                       style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($note['_memberUser']['avatar'])}})">
                    </a>
                    <a href="{{modstart_web_url('note_member/'.$note['memberUserId'])}}" class="tw-flex tw-text-gray-600 tw-font-medium tw-ml-2">{{$note['_memberUser']['username']}}</a>
                    <div class="tw-flex tw-ml-2 tw-items-center tw-text-xs tw-text-gray-400">
                        <time datetime="{{$note['created_at']}}"></time>
                        <span class="tw-px-1">•</span>
                        <span>{{$note['_memberUser']['signature']}}</span>
                    </div>
                </div>
                <div class="tw-flex">
                    <div class="tw-mt-4 tw-flex-grow">
                        <div>
                            <p class="tw-text-gray-600 tw-text-sm">{!! $note['_summary'] !!}</p>
                        </div>
                        <div class="tw-mt-6 tw-flex tw-flex-wrap">
                            <a href="javascript:;" class="tw-flex tw-items-center tw-text-gray-500 hover:tw-opacity-75 tw-mr-4">
                                <i class="iconfont icon-thumb-up tw-mr-1"></i>
                                <span>{{intval($note['viewCount'])}}</span>
                            </a>
                            <a href="javascript:;" class="tw-flex tw-items-center tw-text-gray-500 hover:tw-opacity-75 tw-mr-4">
                                <i class="iconfont icon-pinglun tw-mr-1"></i>
                                <span>{{intval($note['commentCount'])}}</span>
                            </a>
                            <a href="javascript:;" class="tw-flex tw-items-center tw-text-gray-500 hover:tw-opacity-75 tw-mr-4">
                                <i class="iconfont icon-xiangqu tw-mr-1"></i>
                                <span>{{intval($note['likeCount'])}}</span>
                            </a>
                        </div>
                    </div>
                    @if(!empty($note['_cover']))
                        <div class="tw-mt-4 tw-ml-4 tw-flex-shrink-0" style="width:20%;">
                            <div class="ub-cover-3-2 tw-w-full tw-rounded" style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($note['_cover'])}})"></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
@endif
