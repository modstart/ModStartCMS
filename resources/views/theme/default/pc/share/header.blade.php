<header class="ub-header-b">
    <div class="ub-container">
        <div class="menu">
            @if(\Module\Member\Auth\MemberUser::id())
                <a href="{{modstart_web_url('member')}}"><i class="iconfont icon-user"></i> {{\Module\Member\Auth\MemberUser::get('username')}}</a>
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
            @include('module::Vendor.View.searchBox.header')
            @foreach(\Module\Nav\Util\NavUtil::listByPositionWithCache('head') as $nav)
                @if(empty($nav['_child']))
                    <a class="{{modstart_baseurl_active($nav['link'])}}" href="{{$nav['link']}}" {{\Module\Nav\Type\NavOpenType::getBlankAttributeFromValue($nav)}}>{{$nav['name']}}</a>
                @else
                    <div class="nav-item">
                        <div class="sub-title">
                            <a class="{{modstart_baseurl_active($nav['link'])}}" href="{{$nav['link']}}" {{\Module\Nav\Type\NavOpenType::getBlankAttributeFromValue($nav)}}>{{$nav['name']}}</a>
                        </div>
                        <div class="sub-nav">
                            @foreach($nav['_child'] as $child)
                                <a class="sub-nav-item {{modstart_baseurl_active($child['link'])}}" href="{{$child['link']}}" {{\Module\Nav\Type\NavOpenType::getBlankAttributeFromValue($child)}}>{{$child['name']}}</a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        <a class="nav-toggle" href="javascript:;" onclick="$(this).closest('.ub-header-b').toggleClass('show')">
            <i class="show iconfont icon-list"></i>
            <i class="close iconfont icon-close"></i>
        </a>
    </div>
</header>
