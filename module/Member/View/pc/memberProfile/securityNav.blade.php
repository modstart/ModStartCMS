<div class="ub-nav-tab" style="overflow-x:auto;width:100%;white-space:nowrap;overflow-y:hidden;">
    <a class="{{modstart_baseurl_active('member_profile/password')}}" href="{{modstart_web_url('member_profile/password')}}">密码设定</a>
    @if(modstart_config('Member_ProfileEmailEnable',false))
        <a class="{{modstart_baseurl_active('member_profile/email')}}" href="{{modstart_web_url('member_profile/email')}}">邮箱绑定</a>
    @endif
    @if(modstart_config('Member_ProfilePhoneEnable',false))
        <a class="{{modstart_baseurl_active('member_profile/phone')}}" href="{{modstart_web_url('member_profile/phone')}}">手机绑定</a>
    @endif
    @if(modstart_module_enabled('MemberOauth'))
        @foreach(\Module\Member\Config\MemberOauth::get() as $oauth)
            @if($oauth->isSupport())
                <a class="{{modstart_baseurl_active('member_profile/oauth/'.$oauth->name())}}" href="{{modstart_web_url('member_profile/oauth/'.$oauth->name())}}">{{$oauth->title()}}</a>
            @endif
        @endforeach
    @endif
    @if(modstart_config('Member_DeleteEnable',false))
        <a class="ub-text-muted {{modstart_baseurl_active('member_profile/delete')}}" href="{{modstart_web_url('member_profile/delete')}}">注销账号</a>
    @endif
</div>
