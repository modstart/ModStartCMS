@extends($_viewFrame)

<?php $isDialog = \Illuminate\Support\Facades\Input::get('dialog',0); ?>

@section('headAppend')
    @parent
    <link rel="stylesheet" href="@asset('vendor/AigcBase/style/style.css')"/>
    <style>
        header .ub-container{ max-width: 100%; }
    </style>
@endsection

@section('bodyAppend')
    @if(!$isDialog)
        @parent
    @endif
    <script src="@asset('asset/vendor/vue.js')"></script>
    <script src="@asset('asset/vendor/element-ui/index.js')"></script>
    <script>
        window._data = {
            memberUserId: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(\Module\Member\Auth\MemberUser::id()) !!},
            uploadChunkSize: {{\ModStart\Core\Util\EnvUtil::env('uploadMaxSize')}},
            maxUploadFileSize: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(modstart_config('AigcBase_MaxUploadFileSize',1024)*1024*1024) !!},
            doLogin: function (redirect) {
                window.location.href = {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(modstart_web_url('login',['redirect'=>\ModStart\Core\Input\Request::currentPageUrl()])) !!}
            }
        };
        $(function(){
            var $profileDialog = $('[data-profile-box]');
            $profileDialog.on('mouseleave',function(e){
                $profileDialog.addClass('tw-hidden');
            });
            $('[data-profile-button]').on('mouseover',function(e){
                $profileDialog.removeClass('tw-hidden');
            });
        });
        {!! \ModStart\ModStart::lang(['Copy Success','Select Local File',]) !!}
    </script>
@endsection

@section('body')
    @if(!$isDialog)
        @include('theme.default.pc.share.header')
        <div class="pb-aigc-page">
            <div class="aigc-nav">
                <div class="aigc-type">
                    @foreach(\Module\AigcBase\Biz\AigcAppProvider::listAll() as $a)
                        <a href="{{modstart_web_url($a->url())}}"
                           class="item {{modstart_baseurl_active($a->url())}}">
                            <i class="{{$a->icon()}}"></i>
                            {{$a->title()}}
                        </a>
                    @endforeach
                </div>
                <div class="aigc-nav-menu">
                    <a href="javascript:;" data-profile-button class="tw-block tw-text-center">
                        @if(\Module\Member\Auth\MemberUser::isLogin())
                            <img src="{{$_memberUser['avatar']}}"
                                 class="tw-w-10 tw-h-10 tw-rounded-full"
                            />
                        @else
                            <img src="@asset('asset/image/avatar.svg')"
                                 class="tw-w-10 tw-h-10 tw-rounded-full"
                            />
                        @endif
                    </a>
                </div>
            </div>
            <div class="aigc-body">
                @section('aigcBody')
                    <div id="app" class="tw-w-full tw-bg-white">
                        <div class="tw-py-20 tw-text-center">
                            <div>
                                <img src="@asset('vendor/AigcBase/image/loading.svg')" class="tw-h-32" alt="加载中"/>
                            </div>
                            <div class="margin-top-lg ub-text-muted">
                                <i class="iconfont icon-loading"></i>
                                加载中...
                            </div>
                        </div>
                    </div>
                @show
            </div>
        </div>
        <div data-profile-box class="ub-content-box ub-border tw-hidden tw-absolute tw-left-3 tw-bottom-8 tw-w-48 tw-shadow-lg">
            <div class="row">
                <div class="col-6">
                    <a href="javascript:;" data-dialog-title="个人中心" data-dialog-request="{{modstart_web_url('member',['dialog'=>1])}}"
                       class="ub-block tw-text-gray-600 hover:tw-bg-gray-50 tw-rounded-lg tw-py-2 tw-text-center margin-bottom">
                        <div class="tw-w-8 tw-h-8 tw-bg-gray-100 tw-rounded-full tw-mx-auto">
                            <i class="iconfont icon-user tw-w-5" style="font-size:1rem;"></i>
                        </div>
                        <div class="ub-text-sm">
                            个人中心
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="{{modstart_web_url('member_credit')}}"
                       class="ub-block tw-text-gray-600 hover:tw-bg-gray-50 tw-rounded-lg tw-py-2 tw-text-center margin-bottom">
                        <div class="tw-w-8 tw-h-8 tw-bg-gray-100 tw-rounded-full tw-mx-auto">
                            <i class="iconfont icon-credit tw-w-5" style="font-size:1rem;"></i>
                        </div>
                        <div class="ub-text-sm">
                            我的{{modstart_module_config('Member','creditName')}}
                        </div>
                    </a>
                </div>
                @if(modstart_module_enabled('MemberDistribution'))
                    <div class="col-6">
                        <a href="{{modstart_web_url('aigc/distribution')}}"
                           class="ub-block tw-text-gray-600 hover:tw-bg-gray-50 tw-rounded-lg tw-py-2 tw-text-center margin-bottom">
                            <div class="tw-w-8 tw-h-8 tw-bg-gray-100 tw-rounded-full tw-mx-auto">
                                <i class="iconfont icon-share tw-w-5" style="font-size:1rem;"></i>
                            </div>
                            <div class="ub-text-sm">
                                分销推广
                            </div>
                        </a>
                    </div>
                @endif
                <div class="col-6">
                    <a href="javascript:;"
                       data-dialog-request="{{modstart_web_url('feedback/dialog')}}"
                       class="ub-block tw-text-gray-600 hover:tw-bg-gray-50 tw-rounded-lg tw-py-2 tw-text-center margin-bottom">
                        <div class="tw-w-8 tw-h-8 tw-bg-gray-100 tw-rounded-full tw-mx-auto">
                            <i class="iconfont icon-comment tw-w-5" style="font-size:1rem;"></i>
                        </div>
                        <div class="ub-text-sm">
                            意见反馈
                        </div>
                    </a>
                </div>
                <div class="col-6">
                    <a href="javascript:;"
                       data-dialog-request="{{modstart_web_url('site/contact',['dialog'=>1])}}"
                       class="ub-block tw-text-gray-600 hover:tw-bg-gray-50 tw-rounded-lg tw-py-2 tw-text-center margin-bottom">
                        <div class="tw-w-8 tw-h-8 tw-bg-gray-100 tw-rounded-full tw-mx-auto">
                            <i class="iconfont icon-customer tw-w-5" style="font-size:1rem;"></i>
                        </div>
                        <div class="ub-text-sm">
                            联系客服
                        </div>
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="pb-aigc-page pb-aigc-page-dialog">
            <div class="aigc-body">
                @section('aigcBody')
                    <div id="app" class="tw-w-full tw-bg-white">
                        <div class="tw-py-20 tw-text-center">
                            <div>
                                <img src="@asset('vendor/AigcBase/image/loading.svg')" class="tw-h-32" alt="加载中"/>
                            </div>
                            <div class="margin-top-lg ub-text-muted">
                                <i class="iconfont icon-loading"></i>
                                加载中...
                            </div>
                        </div>
                    </div>
                @show
            </div>
        </div>
    @endif
@endsection
