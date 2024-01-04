@extends($_viewMemberFrame)

@section('pageTitleMain')邮箱绑定@endsection
@section('pageKeywords')邮箱绑定@endsection
@section('pageDescription')邮箱绑定@endsection

@section('bodyAppend')
    @parent
    {{\ModStart\ModStart::js('asset/common/commonVerify.js')}}
    <script>
        $(function () {
            new window.api.commonVerify({
                generateServer: '{{$__msRoot}}member_profile/email_verify',
                selectorTarget: 'input[name=email]',
                selectorGenerate: '[data-verify-generate]',
                selectorCountdown: '[data-verify-countdown]',
                selectorRegenerate: '[data-verify-regenerate]',
                selectorCaptcha: 'input[name=captcha]',
                selectorCaptchaImg:'img[data-captcha]',
                interval: 60,
            },window.api.dialog);
        });
    </script>
@endsection

@section('memberBodyContent')

    @include('module::Member.View.pc.memberProfile.securityNav')

    <div class="tw-px-3 tw-py-20 tw-rounded-b-lg tw-bg-white">
        <form action="{{\ModStart\Core\Input\Request::currentPageUrl()}}" class="ub-form" method="post" style="max-width:40em;" data-ajax-form>

            @if(! \Module\Vendor\Provider\MailSender\MailSenderProvider::hasProvider())
                <div class="line">
                    <div class="ub-alert danger">
                        <i class="ub-icon-warning"></i>
                        系统没有开启邮箱发送服务，验证码可能无法发送。
                    </div>
                </div>
            @endif
            @if($_memberUser['email'] && $_memberUser['emailVerified'])
                <div class="line">
                    <div class="label">邮箱:</div>
                    <div class="field">
                        {{$_memberUser['email']}} <span class="ub-text-success">已验证</span>
                    </div>
                </div>
                <div class="line">
                    <div class="label">&nbsp;</div>
                    <div class="field">
                        <a href="javascript:;" onclick="$('[data-modify-box]').show();" class="btn">修改</a>
                    </div>
                </div>
                <div data-modify-box style="display:none;">
                    <div class="line">
                        <div class="label">新邮箱:</div>
                        <div class="field">
                            <input type="text" class="form" name="email" value="" />
                        </div>
                    </div>
                    <div class="line">
                        <div class="label">图形验证：</div>
                        <div class="field">
                            <div class="row">
                                <div class="col-4">
                                    <img data-captcha src="{{$__msRoot}}member_profile/captcha" style="width:100%;height:30px;border:1px solid #CCC;border-radius:3px;cursor:pointer;" alt="刷新验证码" onclick="this.src='{{$__msRoot}}member_profile/captcha?'+Math.random();"/>
                                </div>
                                <div class="col-4">
                                    <input class="form" type="text" name="captcha" />
                                </div>
                                <div class="col-4">
                                    <button class="btn btn-round btn-default btn-block" type="button" data-verify-generate>获取验证码</button>
                                    <button class="btn btn-round btn-default btn-block" type="button" data-verify-countdown style="display:none;margin:0;"></button>
                                    <button class="btn btn-round btn-default btn-block" type="button" data-verify-regenerate style="display:none;margin:0;">重新获取</button>
                                </div>
                            </div>
                            <div class="help">
                            </div>
                        </div>
                    </div>
                    <div class="line">
                        <div class="label">邮箱验证：</div>
                        <div class="field">
                            <input type="text" class="form" name="verify" />
                        </div>
                    </div>
                    <div class="line">
                        <div class="label">&nbsp;</div>
                        <div class="field">
                            <button type="submit" class="btn btn-round btn-primary">提交</button>
                        </div>
                    </div>
                </div>
            @else
                @if($_memberUser['email'])
                    <div class="line">
                        <div class="ub-alert danger">
                            邮箱还没有进行验证
                        </div>
                    </div>
                @endif
                <div class="line">
                    <div class="label">邮箱:</div>
                    <div class="field">
                        <input type="text" class="form" name="email" value="{{empty($_memberUser['email'])?'':$_memberUser['email']}}" />
                    </div>
                </div>
                <div class="line">
                    <div class="label">图形验证：</div>
                    <div class="field">
                        <div class="row">
                            <div class="col-4">
                                <img data-captcha src="{{$__msRoot}}member_profile/captcha" style="width:100%;height:30px;border:1px solid #CCC;border-radius:3px;cursor:pointer;" alt="刷新验证码" onclick="this.src='{{$__msRoot}}member_profile/captcha?'+Math.random();"/>
                            </div>
                            <div class="col-4">
                                <input type="text" class="form" name="captcha" />
                            </div>
                            <div class="col-4">
                                <button class="btn btn-round btn-default btn-block" type="button" data-verify-generate>获取验证码</button>
                                <button class="btn btn-round btn-default btn-block" type="button" data-verify-countdown style="display:none;margin:0;"></button>
                                <button class="btn btn-round btn-default btn-block" type="button" data-verify-regenerate style="display:none;margin:0;">重新获取</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="line">
                    <div class="label">邮箱验证：</div>
                    <div class="field">
                        <input type="text" class="form" name="verify" />
                    </div>
                </div>
                <div class="line">
                    <div class="label">&nbsp;</div>
                    <div class="field">
                        <button type="submit" class="btn btn-round btn-primary">提交</button>
                    </div>
                </div>
            @endif
        </form>
    </div>
@endsection
