@extends($_viewFrame)

@section('pageTitleMain')验证手机找回密码@endsection
@section('pageKeywords')验证手机找回密码@endsection
@section('pageDescription')验证手机找回密码@endsection

@section('headAppend')
    @parent
    <link rel="canonical" href="{{modstart_web_url('retrieve/phone')}}"/>
@endsection

@section('bodyAppend')
    @parent
    {{\ModStart\ModStart::js('asset/common/commonVerify.js')}}
    <script>
        $(function () {
            new window.api.commonVerify({
                generateServer: '{{$__msRoot}}retrieve/phone_verify',
                selectorTarget: 'input[name=phone]',
                selectorGenerate: '[data-phone-verify-generate]',
                selectorCountdown: '[data-phone-verify-countdown]',
                selectorRegenerate: '[data-phone-verify-regenerate]',
                selectorCaptcha: 'input[name=captcha]',
                selectorCaptchaImg:'img[data-captcha]',
                interval: 60,
            },window.api.dialog);
        });
    </script>
@endsection

@section('bodyContent')

    <div class="ub-account">

        <div class="box">

            <div class="nav">
                <a href="javascript:;" class="active">
                    <i class="iconfont icon-phone"></i>
                    验证手机找回密码
                </a>
            </div>

            @include('module::Member.View.pc.inc.retrieveNav',['current'=>1])

            <div class="ub-form flat">
                <form action="{{\ModStart\Core\Input\Request::currentPageUrl()}}" method="post" data-ajax-form>
                    <div class="line">
                        <div class="field">
                            <div class="row no-gutters">
                                <div class="col-7">
                                    <input type="text" class="form-lg" name="captcha" autocomplete="off" placeholder="图片验证码" />
                                </div>
                                <div class="col-5">
                                    <img class="captcha captcha-lg" data-captcha title="刷新验证" onclick="this.src='{{$__msRoot}}retrieve/captcha?'+Math.random()" src="{{$__msRoot}}retrieve/captcha?{{time()}}" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="line">
                        <div class="field">
                            <div class="row no-gutters">
                                <div class="col-7">
                                    <input type="text" class="form-lg" name="phone" placeholder="输入手机" />
                                </div>
                                <div class="col-5">
                                    <button class="btn btn-round btn-lg btn-block" type="button" data-phone-verify-generate>获取验证码</button>
                                    <button class="btn btn-round btn-lg btn-block" type="button" data-phone-verify-countdown style="display:none;margin:0;"></button>
                                    <button class="btn btn-round btn-lg btn-block" type="button" data-phone-verify-regenerate style="display:none;margin:0;">重新获取</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="line">
                        <div class="field">
                            <input type="text" class="form-lg" name="verify" placeholder="手机验证码" />
                        </div>
                    </div>
                    <div class="line">
                        <div class="field">
                            <button type="submit" class="btn btn-round btn-primary btn-lg btn-block">提交</button>
                        </div>
                    </div>
                    <input type="hidden" name="redirect" value="{{empty($redirect)?'':$redirect}}" />
                </form>
            </div>
        </div>

    </div>

@endsection
