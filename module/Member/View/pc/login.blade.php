@extends($_viewFrame)

@section('pageTitleMain')登录@endsection

@section('headAppend')
    @parent
    {!! \ModStart\Core\Hook\ModStartHook::fireInView('MemberLoginPageHeadAppend',$this); !!}
@endsection

@section('bodyAppend')
    @parent
    {!! \ModStart\Core\Hook\ModStartHook::fireInView('MemberLoginPageBodyAppend',$this); !!}
@endsection

@section('bodyContent')

    <div class="ub-account lg:tw-flex lg:tw-flex-col lg:tw-justify-center" style="min-height:calc( 100vh - 170px );">

        <div class="box wide">
            <div class="tw-py-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="nav lg:tw-text-left lg:tw-px-10">
                            <a href="javascript:;" class="active">登录</a>
                            @if(!modstart_config('registerDisable',false))
                                ·
                                <a href="{{$__msRoot}}register?redirect={{!empty($redirect)?urlencode($redirect):''}}">注册</a>
                            @endif
                        </div>

                        <div class="ub-form flat">
                            <form action="?" method="post" data-ajax-form>
                                <div class="line">
                                    <div class="field">
                                        <input type="text" class="form-lg" name="username" placeholder="输入用户" />
                                    </div>
                                </div>
                                <div class="line">
                                    <div class="field">
                                        <input type="password" class="form-lg" name="password" placeholder="输入密码" />
                                    </div>
                                </div>
                                @if(modstart_config('loginCaptchaEnable',false))
                                    <div class="line">
                                        <div class="field">
                                            <div class="row no-gutters">
                                                <div class="col-6">
                                                    <input type="text" class="form-lg" name="captcha" autocomplete="off" placeholder="图片验证码" />
                                                </div>
                                                <div class="col-6">
                                                    <img class="captcha captcha-lg" title="刷新验证" data-captcha
                                                         src="{{modstart_web_url('login/captcha')}}"
                                                         onclick="$(this).attr('src','{{modstart_web_url('login/captcha')}}?'+Math.random())" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="line">
                                    <div class="field">
                                        <button type="submit" class="btn btn-primary btn-lg btn-block">登录</button>
                                        <input type="hidden" name="redirect" value="{{$redirect or ''}}">
                                    </div>
                                </div>
                            </form>
                        </div>

                        @include('module::Member.View.pc.oauthButtons')

                        @if(!modstart_config('retrieveDisable',false))
                            <div class="retrieve">
                                忘记密码?
                                <a href="{{$__msRoot}}retrieve?redirect={{urlencode($redirect)}}">找回密码</a>
                            </div>
                        @endif
                    </div>
                    <div class="tw-hidden col-md-6 lg:tw-flex lg:tw-flex-col lg:tw-justify-center">
                        <div class="lg:tw-flex-grow tw-rounded-r tw--my-11 tw-flex tw-flex-col tw-justify-center tw-bg-no-repeat tw-bg-center tw-bg-cover tw-relative tw-overflow-hidden"
                             style="background-image:url( @asset('vendor/Member/image/login_bg.png') )" data-member-login-right>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
