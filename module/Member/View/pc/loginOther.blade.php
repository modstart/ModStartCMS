@extends($_viewFrame)

@section('pageTitleMain')登录@endsection
@section('pageKeywords')登录@endsection
@section('pageDescription')登录@endsection

@section('headAppend')
    @parent
    <link rel="canonical" href="{{modstart_web_url('login')}}"/>
    {!! \ModStart\Core\Hook\ModStartHook::fireInView('MemberLoginPageHeadAppend'); !!}
@endsection

@section('bodyAppend')
    @parent
    {!! \ModStart\Core\Hook\ModStartHook::fireInView('MemberLoginPageBodyAppend'); !!}
@endsection

@section('bodyContent')

    <div class="ub-account pb-member-login-account">

        <div class="box" data-member-login-box>
            <div style="min-height:15rem;" data-member-login-other></div>
        </div>

        @include('module::Member.View.pc.oauthButtons')

        @if(!modstart_config('retrieveDisable',false))
            <div class="retrieve">
                忘记密码?
                <a target="_parent"
                   href="{{modstart_web_url('retrieve',['redirect'=>empty($redirect)?null:$redirect])}}">找回密码</a>
            </div>
        @endif

    </div>


@endsection
