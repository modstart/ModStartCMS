@extends($_viewMemberFrame)

@section('pageTitleMain')绑定的账号@endsection
@section('pageKeywords')绑定的账号@endsection
@section('pageDescription')绑定的账号@endsection

@section('memberBodyContent')

    @include('module::Member.View.pc.memberProfile.securityNav')

    <div class="tw-px-3 tw-py-20 tw-rounded-b-lg tw-bg-white">
        @if(empty($oauthRecord))
            <div class="ub-alert warning">
                当前账号暂未绑定 {{$oauth->title()}}
                @if($oauth->bindRender())
                    {!! $oauth->bindRender() !!}
                @else
                    <a class="btn btn-round btn-primary" rel="nofollow"
                       href="{{modstart_web_url('oauth_login_'.$oauth->name(),['redirect'=>\ModStart\Core\Input\Request::currentPageUrl()])}}">立即绑定</a>
                @endif
            </div>
        @else
            <div class="ub-alert success">
                当前账号已绑定 {{$oauth->title()}}
            </div>
            <div class="tw-my-4">
                <div class="tw-bg-gray-100 tw-rounded tw-mb-2 tw-box tw-px-5 tw-py-3 tw-mb-3 tw-flex tw-items-center tw-zoom-in"
                     data-repeat="3">
                    <div class="tw-w-10 tw-h-10 tw-flex-none tw-image-fit tw-rounded-full tw-overflow-hidden">
                        <div class="ub-cover-1-1 circle"
                             style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fixOrDefault($oauthRecord['infoAvatar'],'asset/image/avatar.svg')}})"></div>
                    </div>
                    <div class="tw-ml-4 tw-mr-auto">
                        <div class="tw-font-medium">{{$oauthRecord['infoUsername']}}</div>
                        <div class="tw-text-gray-600 tw-text-xs tw-mt-0.5">{{$oauthRecord['created_at']}}</div>
                    </div>
                    <div class="tw-text-red-600">
                        <a href="javascript:;" class="btn btn-round btn-danger" data-confirm="确定解绑？" data-ajax-request-loading data-ajax-request="{{modstart_api_url('member_profile/oauth_unbind',['type'=>$oauth->name()])}}">解绑</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
