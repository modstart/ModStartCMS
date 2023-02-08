@extends('modstart::admin.frame')

@section('headAppend')
    @parent
    <style type="text/css">
        body{
            min-height:100vh;
            background-color:#FFFFFF;
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
        }
    </style>
    {!! \ModStart\Core\Hook\ModStartHook::fireInView('AdminLoginHeadAppend'); !!}
@endsection

@section('bodyAppend')
    @parent
    {!! \ModStart\Core\Hook\ModStartHook::fireInView('AdminLoginBodyAppend'); !!}
@endsection

@section('body')
    <div class="ub-admin-login">
        <div class="login-box">
            <div class="info">
                <div class="title">
                    <i class="iconfont icon-ms"></i> {!! L('Admin Login') !!}
                </div>
                <div class="slogan">
                    @if(modstart_config('adminLoginSlogan'))
                        {{ modstart_config('adminLoginSlogan') }}
                    @else
                        {{\ModStart\Core\Util\MetaUtil::get('APP_NAME')}}
                    @endif
                </div>
            </div>
            <div class="form">
                <form class="ub-form" method="post" action="?" data-ajax-form>
                    @if(config('modstart.admin.login.captcha',false) && $captchaProvider && $captchaProvider->name()=='sms' && modstart_config('AdminManagerEnhance_SmsCaptchaQuick',false))
                        {{--Ignore Username and password--}}
                    @else
                        <div class="line">
                            <i class="iconfont icon-user"></i>
                            {{ L('Username') }}
                            <input type="text" name="username" value="{{\Illuminate\Support\Facades\Input::get('username','')}}" placeholder="{{ L('Please Input') }}"/>
                        </div>
                        <div class="line">
                            <i class="iconfont icon-lock"></i>
                            {{ L('Password') }}
                            <input type="password" name="password" value="{{\Illuminate\Support\Facades\Input::get('password','')}}" placeholder="{{ L('Please Input') }}"/>
                        </div>
                    @endif
                    @if(config('modstart.admin.login.captcha',false))
                        @if($captchaProvider)
                            <div style="padding:0.5rem;">
                                {!! $captchaProvider->render() !!}
                            </div>
                        @else
                            <div class="line">
                                <i class="iconfont icon-robot"></i>
                                {{ L('Captcha') }}
                                <div class="row">
                                    <div class="col-6">
                                        <input type="text" name="captcha" value="" autocomplete="off" placeholder="{{ L('Please Input') }}"/>
                                    </div>
                                    <div class="col-6">
                                        <img data-captcha style="height:40px;width:100%;border:1px solid #CCC;border-radius:3px;" data-uk-tooltip title="{{ L('Click To Refresh') }}" src="{{action('\ModStart\Admin\Controller\AuthController@loginCaptcha')}}?{{time()}}" onclick="this.src='{{action('\ModStart\Admin\Controller\AuthController@loginCaptcha')}}?'+Math.random();" />
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                    <div class="line">
                        <input type="hidden" name="redirect" value="{{$redirect}}">
                        <button type="submit" class="btn btn-block btn-lg btn-primary">
                            {{ L('Submit') }}
                            <i class="iconfont icon-right"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
