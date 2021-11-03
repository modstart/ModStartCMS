@if(empty($users))
    <div class="ub-empty">
        暂无记录
    </div>
@else
    @foreach($users as $user)
        <div class="tw-py-1 tw-flex tw-justify-between tw-items-center">
            <div class="tw-flex">
                <div class="tw-mr-4">
                    <a href="{{modstart_web_url('note_member/'.$user['id'])}}" class="ub-cover-1-1 tw-shadow tw-w-10 tw-h-10 tw-rounded-full"
                       style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($user['avatar'])}});">
                    </a>
                </div>
                <div>
                    <a href="{{modstart_web_url('note_member/'.$user['id'])}}" class="tw-font-bold tw-text-gray-700">{{$user['username']}}</a>
                    <div class="tw-text-gray-400 tw-text-sm">{{$user['signature']}}</div>
                </div>
            </div>
            @if($user['id']!=\Module\Member\Auth\MemberUser::id())
                <div data-member-follow-item data-status="{{$user['_isFollow']?'is_follow':'not_follow'}}" data-id="{{$user['id']}}">
                    <a href="javascript:;" data-action="follow" class="btn btn-primary-line btn-round">
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
