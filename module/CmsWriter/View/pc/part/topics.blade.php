@if(empty($topics))
    <div class="ub-empty">
        <div class="icon">
            <div class="iconfont icon-empty-box"></div>
        </div>
        <div class="text">暂无记录</div>
    </div>
@else
    @foreach($topics as $topic)
        <div class="tw-rounded-lg tw-mb-2 tw-flex tw-justify-between tw-items-center tw-bg-white" data-repeat="3">
            <div class="tw-flex">
                <div class="tw-mr-4">
                    <a href="{{modstart_web_url('t/'.$topic['alias'])}}" class="ub-cover-1-1 tw-shadow tw-w-10 tw-h-10 tw-rounded"
                         style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($topic['cover'])}});">
                    </a>
                </div>
                <div>
                    <a href="{{modstart_web_url('t/'.$topic['alias'])}}" class="tw-font-bold tw-text-gray-700">{{$topic['title']}}</a>
                    <div class="tw-text-gray-400 tw-text-sm">{{$topic['description']}}</div>
                </div>
            </div>
            @if(isset($topic['_isFollow']))
                <div data-topic-follow-item data-status="{{$topic['_isFollow']?'is_follow':'not_follow'}}" data-alias="{{$topic['alias']}}">
                    <a href="javascript:;" data-action="follow" class="btn btn-primary btn-round">
                        <i class="iconfont icon-plus"></i>
                        关注
                    </a>
                    <a href="javascript:;" data-action="unfollow" class="btn btn-round">
                        <i class="iconfont icon-check"></i>
                        已关注
                    </a>
                </div>
            @endif
        </div>
    @endforeach
@endif
