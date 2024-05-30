<div class="ub-account" style="min-height:calc( 100vh - 220px );">

    <div class="box">

        <div class="nav">
            <a href="{{$__msRoot}}login?redirect={{!empty($redirect)?urlencode($redirect):''}}">登录</a>

            ·
            <a href="javascript:;" class="active">注册</a>
        </div>

        @if(!empty($registerPageTitle))
            {!! $registerPageTitle !!}
        @endif

        <div class="ub-form flat">
            <form action="{{\ModStart\Core\Input\Request::currentPageUrl()}}" method="post" data-ajax-form>
                <div class="line">
                    <div class="field">
                        <input type="text" class="form-lg" name="phone" placeholder="输入手机"/>
                    </div>
                </div>
                @include('module::Member.View.pc.inc.registerCaptcha')
                <div class="line">
                    <div class="field">
                        <div class="row no-gutters">
                            <div class="col-7">
                                <input type="text" class="form-lg" name="phoneVerify" placeholder="输入验证码"/>
                            </div>
                            <div class="col-5">
                                <button class="btn btn-round btn-lg btn-block" type="button" data-phone-verify-generate>获取验证码
                                </button>
                                <button class="btn btn-round btn-lg btn-block" type="button" data-phone-verify-countdown
                                        style="display:none;margin:0;"></button>
                                <button class="btn btn-round btn-lg btn-block" type="button" data-phone-verify-regenerate
                                        style="display:none;margin:0;">重新获取
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @foreach(\Module\Member\Provider\RegisterProcessor\MemberRegisterProcessorProvider::listAll() as $provider)
                    {!! $provider->render() !!}
                @endforeach
                @if(modstart_config('Member_RegisterPhonePasswordEnable',false))
                    <div class="line">
                        <div class="field">
                            <input type="password" class="form-lg" name="password" placeholder="设置登录密码"/>
                        </div>
                    </div>
                @endif
                <div class="line">
                    <div class="field">
                        <button type="submit" class="btn btn-round btn-primary btn-lg btn-block">注册并登录</button>
                    </div>
                </div>
                @if(modstart_config('Member_AgreementEnable',false)||modstart_config('Member_PrivacyEnable',false))
                    <div class="line">
                        <div class="field">
                            <input type="checkbox" name="agreement" value="1" checked class="tw-align-middle"/>
                            @if(modstart_config('Member_AgreementEnable',false))
                                <a href="{{modstart_web_url('member/agreement')}}"
                                   target="_blank">{{modstart_config('Member_AgreementTitle','用户使用协议')}}</a>
                            @endif
                            @if(modstart_config('Member_PrivacyEnable',false))
                                <a href="{{modstart_web_url('member/privacy')}}"
                                   target="_blank">{{modstart_config('Member_PrivacyTitle','用户隐私协议')}}</a>
                            @endif
                        </div>
                    </div>
                @endif
            </form>
        </div>

    </div>

</div>
