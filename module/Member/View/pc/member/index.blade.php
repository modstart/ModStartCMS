@extends($_viewMemberFrame)

@section('pageTitleMain'){{'我的'}}@endsection

@section('memberBodyContent')
    <div class="tw-bg-white tw-rounded tw-shadow">
        <div class="tw-flex tw-p-4 tw-flex-wrap">
            <div class="tw-flex-shrink-0 tw-w-14">
                <a href="{{modstart_web_url('member_profile/avatar')}}" class="tw-block tw-w-10 tw-h-10 ub-cover-1-1 tw-rounded-full tw-shadow"
                   style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($_memberUser['avatar'])}})"></a>
            </div>
            <div class="tw-flex-grow">
                <div class="tw-text-bold">{{$_memberUser['username']}}</div>
                @if(\ModStart\Module\ModuleManager::getModuleConfigBoolean('Member', 'vipEnable'))
                    <div class="tw-py-1 ub-text-primary">
                        @if(\Module\Member\Auth\MemberVip::get('icon'))
                            <img src="{{\ModStart\Core\Assets\AssetsUtil::fix(\Module\Member\Auth\MemberVip::get('icon'))}}"
                                 class="tw-h-4"
                                 alt="{{\Module\Member\Auth\MemberVip::get('title')}}" />
                        @endif
                        {{\Module\Member\Auth\MemberVip::get('title')}}
                    </div>
                @endif
                <div class="tw-text-gray-400">{{$_memberUser['signature'] or '暂无签名'}}</div>
{{--                <div class="tw-flex tw-mt-3">--}}
{{--                    <a href="{{modstart_web_url('wenda/member/'.$memberUser['id'].'/replies')}}" class="tw-text-center tw-pr-4 tw-text-sm">--}}
{{--                        <div class="tw-text-gray-300">回答</div>--}}
{{--                        <div class="tw-text-gray-900">{{$memberUser['_replyCount'] or '0'}}</div>--}}
{{--                    </a>--}}
{{--                </div>--}}
            </div>
{{--            <div class="tw-flex tw-items-start tw-w-full tw-flex-shrink-0 tw-ml-20 tw-mt-4 lg:tw-w-auto">--}}
{{--                <a href="{{modstart_web_url('wenda/ask/edit',['invite'=>$memberUser['id']])}}" class="btn btn-primary tw-mr-1">--}}
{{--                    <i class="iconfont icon-comment"></i>--}}
{{--                    提问--}}
{{--                </a>--}}
{{--            </div>--}}
        </div>
    </div>

    <div class="margin-top">
        {!! $content !!}
    </div>

@endsection
