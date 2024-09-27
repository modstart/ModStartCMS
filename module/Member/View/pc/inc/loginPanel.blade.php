<div class="ub-account pb-member-login-account">

    <div class="box" data-member-login-box>

        <div class="nav">
            <a href="javascript:;" class="active">登录</a>
            @if(!modstart_config('registerDisable',false))
                ·
                <a href="{{$__msRoot}}register?redirect={{!empty($redirect)?urlencode($redirect):''}}">注册</a>
            @endif
        </div>
        <div class="ub-form flat">
            <form action="{{modstart_web_url('login')}}" method="post" data-ajax-form>
                @if(modstart_config('Member_LoginInfoEncrypt',false))
                    <input type="hidden" data-encrypt-data name="ek" value="{{\ModStart\Core\Util\RandomUtil::string(8)}}" />
                @endif
                <div class="line">
                    <div class="field">
                        <input type="text" class="form-lg" name="username" placeholder="输入用户"
                               @if(modstart_config('Member_LoginInfoEncrypt',false)) data-encrypt-field="username" @endif/>
                    </div>
                </div>
                <div class="line">
                    <div class="field">
                        <input type="password" class="form-lg" name="password" placeholder="输入密码"
                               @if(modstart_config('Member_LoginInfoEncrypt',false)) data-encrypt-field="password" @endif/>
                    </div>
                </div>
                @if(modstart_config('loginCaptchaEnable',false))
                    @if($provider = \Module\Member\Util\SecurityUtil::loginCaptchaProvider())
                        <div style="padding:0.5rem;">
                            {!! $provider->render() !!}
                        </div>
                    @else
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
                @endif
                <div class="line">
                    <div class="field">
                        <button type="submit" class="btn btn-round btn-primary btn-lg btn-block">登录</button>
                        <input type="hidden" name="redirect" value="{{empty($redirect)?'':$redirect}}">
                    </div>
                </div>
            </form>
        </div>

        @include('module::Member.View.pc.oauthButtons')

        @if(!modstart_config('retrieveDisable',false))
            <div class="retrieve">
                忘记密码?
                <a target="_parent" href="{{modstart_web_url('retrieve',['redirect'=>empty($redirect)?null:$redirect])}}">找回密码</a>
            </div>
        @endif

    </div>

</div>
