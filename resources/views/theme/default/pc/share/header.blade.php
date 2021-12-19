<header class="ub-header-b">
    <div class="ub-container">
        <div class="menu">
            @if(\Module\Member\Auth\MemberUser::id())
                <a href="{{modstart_web_url('member_message')}}">
                    <i class="iconfont icon-bell"></i>
                    <?php $count = \Module\Member\Util\MemberMessageUtil::getUnreadMessageCount(\Module\Member\Auth\MemberUser::id()); ?>
                    @if($count)
                        <span class="badge" data-member-unread-message-count>{{$count}}</span>
                    @endif
                </a>
                <a href="{{modstart_web_url('member')}}"><i class="iconfont icon-user"></i> {{\Module\Member\Auth\MemberUser::get('username')}}</a>
                <a href="javascript:;" data-confirm="确认退出？" data-href="/logout">退出</a>
            @else
                <a href="{{modstart_web_url('login')}}">登录</a>
                @if(!modstart_config('registerDisable',false))
                    <a href="{{modstart_web_url('register')}}">注册</a>
                @endif
            @endif
        </div>
        <div class="logo">
            <a href="{{modstart_web_url('')}}">
                <img src="{{\ModStart\Core\Assets\AssetsUtil::fix(modstart_config('siteLogo'))}}"/>
            </a>
        </div>
        <div class="nav-mask" onclick="$(this).closest('.ub-header-b').removeClass('show')"></div>
        <div class="nav">
            @foreach(\Module\Nav\Util\NavUtil::listByPositionWithCache('head') as $nav)
                <a class="{{modstart_baseurl_active($nav['link'])}}" href="{{$nav['link']}}" {{\Module\Nav\Type\NavOpenType::getBlankAttributeFromValue(empty($nav['openType'])?null:$nav['openType'])}}>{{$nav['name']}}</a>
            @endforeach
        </div>
        <a class="nav-toggle" href="javascript:;" onclick="$(this).closest('.ub-header-b').toggleClass('show')">
            <i class="show iconfont icon-list"></i>
            <i class="close iconfont icon-close"></i>
        </a>
    </div>
</header>
