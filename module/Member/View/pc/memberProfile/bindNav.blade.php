<div class="ub-nav-tab" style="overflow-x:auto;width:100%;white-space:nowrap;overflow-y:hidden;">
    <a class="{{modstart_baseurl_active('member_profile/email')}}" href="{{modstart_web_url('member_profile/email')}}">邮箱绑定</a>
    <a class="{{modstart_baseurl_active('member_profile/phone')}}" href="{{modstart_web_url('member_profile/phone')}}">手机绑定</a>
    @if(\ModStart\Module\ModuleManager::isModuleEnabled('MemberOauth'))
        @foreach(\Module\Member\Config\MemberOauth::get() as $oauth)
            <a class="{{modstart_baseurl_active('member_profile/oauth/'.$oauth->name())}}" href="{{modstart_web_url('member_profile/oauth/'.$oauth->name())}}">{{$oauth->title()}}</a>
        @endforeach
    @endif
</div>
