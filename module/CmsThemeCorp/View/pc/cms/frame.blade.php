@extends('theme.default.pc.frame')

@section('pageTitle'){{modstart_config('siteName')}} - {{modstart_config('siteSlogan')}}@endsection

@section('headAppend')
    @parent
    <link rel="stylesheet" href="@asset('vendor/CmsThemeCorp/css/bootstrap.css')">
    <link rel="stylesheet" href="@asset('vendor/CmsThemeCorp/css/maicons.css')">
    <link rel="stylesheet" href="@asset('vendor/CmsThemeCorp/vendor/animate/animate.css')">
    <link rel="stylesheet" href="@asset('vendor/CmsThemeCorp/vendor/owl-carousel/css/owl.carousel.css')">
    <link rel="stylesheet" href="@asset('vendor/CmsThemeCorp/vendor/fancybox/css/jquery.fancybox.css')">
    <link rel="stylesheet" href="@asset('vendor/CmsThemeCorp/css/theme.css')">
@endsection

@section('htmlProperties'){!! 'class="bs-styles"' !!}@endsection

@section('bodyAppend')
    @parent
    <script src="@asset('vendor/CmsThemeCorp/js/bootstrap.bundle.min.js')"></script>
    <script src="@asset('vendor/CmsThemeCorp/vendor/owl-carousel/js/owl.carousel.min.js')"></script>
    <script src="@asset('vendor/CmsThemeCorp/vendor/wow/wow.min.js')"></script>
    <script src="@asset('vendor/CmsThemeCorp/vendor/fancybox/js/jquery.fancybox.min.js')"></script>
    <script src="@asset('vendor/CmsThemeCorp/vendor/isotope/isotope.pkgd.min.js')"></script>
    <script src="@asset('vendor/CmsThemeCorp/js/theme.js')"></script>
@endsection

@section('body')

    <!-- Back to top button -->
    <div class="back-to-top"></div>

    <header>
        <div class="top-bar">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-inline-block">
                            <span class="mai-mail fg-primary"></span> <a
                                    href="mailto:{{modstart_config('Cms_ContactEmail','[邮箱]')}}">{{modstart_config('Cms_ContactEmail','[邮箱]')}}</a>
                        </div>
                        <div class="d-inline-block ml-2">
                            <span class="mai-call fg-primary"></span> <a
                                    href="tel:{{modstart_config('Cms_ContactPhone','[电话]')}}">{{modstart_config('Cms_ContactPhone','[电话]')}}</a>
                        </div>
                    </div>
                    <div class="col-md-4 text-right d-none d-md-block">
                        <div class="social-mini-button">
                            @if(\Module\Member\Auth\MemberUser::id())
                                {{--                <a class="ub-color-vip" href="/member_vip">--}}
                                {{--                    <i class="iconfont icon-vip"></i>--}}
                                {{--                    {{\Module\Member\Auth\MemberVip::get('title')}}--}}
                                {{--                </a>--}}
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
                                <a href="{{modstart_web_url('register')}}">注册</a>
                            @endif
                            {{--                                <a href="#"><span class="mai-logo-facebook-f"></span></a>--}}
                            {{--                                <a href="#"><span class="mai-logo-twitter"></span></a>--}}
                            {{--                                <a href="#"><span class="mai-logo-youtube"></span></a>--}}
                            {{--                                <a href="#"><span class="mai-logo-linkedin"></span></a>--}}
                        </div>
                    </div>
                </div>
            </div> <!-- .container -->
        </div> <!-- .top-bar -->

        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a href="{{modstart_web_url('')}}" class="navbar-brand">
                    <img class="tw-h-20"
                         src="{{\ModStart\Core\Assets\AssetsUtil::fix(modstart_config('siteLogo'),'/placeholder/200x50')}}"/>
                </a>

                <button class="navbar-toggler" data-toggle="collapse" data-target="#navbarContent"
                        aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="navbar-collapse collapse" id="navbarContent">
                    <ul class="navbar-nav ml-auto pt-3 pt-lg-0">
                        @foreach(\Module\Nav\Util\NavUtil::listByPositionWithCache('head') as $nav)
                            <li class="nav-item {{modstart_baseurl_active($nav['link'])}}">
                                <a href="{{$nav['link']}}"
                                   class="nav-link" {{\Module\Nav\Type\NavOpenType::getBlankAttributeFromValue(empty($nav['openType'])?null:$nav['openType'])}}>{{$nav['name']}}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div> <!-- .container -->
        </nav> <!-- .navbar -->

    </header>

    @section('bodyContent')

    @show

    <footer class="page-footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 py-3">
                    <h3>{{modstart_config('siteName','[网站名称]')}}</h3>
                </div>
                <div class="col-lg-4 py-3">
                    <h5>联系信息</h5>
                    <p>{{modstart_config('Cms_ContactAddress','[联系地址]')}}</p>
                    <p>邮箱: {{modstart_config('Cms_ContactEmail','[邮箱]')}}</p>
                    <p>电话: {{modstart_config('Cms_ContactPhone','[电话]')}}</p>
                </div>
                <div class="col-lg-4 py-3">
                    <h5>{{modstart_config('Cms_CompanyName','[企业名称]')}}</h5>
                    <ul class="footer-menu">
                        @foreach(\Module\Nav\Util\NavUtil::listByPositionWithCache('foot') as $nav)
                            <li>
                                <a href="{{$nav['link']}}" {{\Module\Nav\Type\NavOpenType::getBlankAttributeFromValue(empty($nav['openType'])?null:$nav['openType'])}}>{{$nav['name']}}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <hr>

            <div class="row mt-4">
                <div class="col-md-6">
                    <p>
                        &copy;{{modstart_config('siteDomain','[网站域名]')}}
                        <a href="http://beian.miit.gov.cn" class="tw-text-gray-400" target="_blank">{{modstart_config('siteBeian','[网站备案信息]')}}</a>
                    </p>
                </div>
                <div class="col-md-6 text-right">
                    <div class="sosmed-button">
                        <a href="#"><span class="mai-logo-facebook-f"></span></a>
                        <a href="#"><span class="mai-logo-twitter"></span></a>
                        <a href="#"><span class="mai-logo-youtube"></span></a>
                        <a href="#"><span class="mai-logo-linkedin"></span></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

@endsection
