<header class="ub-header-b">
    <div class="ub-container">
        <div class="menu">
            @if(\Module\Member\Auth\MemberUser::id())
                <div class="item">
                    <a href="{{modstart_web_url('member')}}" class="sub-title">
                        <i class="iconfont icon-user"></i>
                        {{\Module\Member\Auth\MemberUser::get('username')}}
                    </a>
                    <div class="sub-nav">
                        {!! \Module\Member\Config\MemberNavMenu::render() !!}
                        <a class="sub-nav-item" href="javascript:;"
                           data-href="{{modstart_web_url('logout')}}" data-confirm="确认退出登录？">
                            退出登录
                        </a>
                    </div>
                </div>
            @else
                <a href="{{modstart_web_url('login',['redirect'=>\ModStart\Core\Input\Request::redirectUrl()])}}" rel="nofollow">
                    <i class="iconfont icon-user"></i> 
                    登录
                </a>
            @endif
        </div>
        <div class="logo">
            <a href="{{modstart_web_url('')}}">
                <img src="{{\ModStart\Core\Assets\AssetsUtil::fix(modstart_config('siteLogo'))}}"/>
            </a>
        </div>
        <div class="nav-mask" onclick="MS.header.hide()"></div>
        <div class="nav">
            @include('module::Vendor.View.searchBox.header')
            {!! \Module\Nav\View\NavView::position('head') !!}
        </div>
        <a class="nav-toggle" href="javascript:;" onclick="MS.header.trigger()">
            <i class="show iconfont icon-list"></i>
            <i class="close iconfont icon-close"></i>
        </a>
    </div>
</header>
