@if($provider=\Module\Member\Util\SecurityUtil::registerCaptchaProvider())
    <div style="padding:0.5rem;">
        <div>
            {!! $provider->render() !!}
        </div>
        <div class="tw-text-left">
            <text class="ub-text-muted" data-captcha-status="tip"><i class="iconfont icon-warning"></i> 等待验证</text>
            <text class="ub-text-muted" data-captcha-status="loading" style="display:none;"><i class="iconfont icon-refresh"></i> 正在验证</text>
            <text class="ub-text-success" data-captcha-status="success" style="display:none;"><i class="iconfont icon-checked"></i> 验证通过</text>
            <text class="ub-text-danger" data-captcha-status="error" style="display:none;"><i class="iconfont icon-close-o"></i> 验证失败</text>
        </div>
    </div>
@else
    <div class="line">
        <div class="field">
            <div class="row no-gutters">
                <div class="col-7">
                    <input type="text" class="form-lg" name="captcha" autocomplete="off"
                           onfocus="$(this).attr('data-form-process','processing')"
                           onblur="__memberCheckCaptcha()" placeholder="图片验证码" />
                </div>
                <div class="col-5">
                    <img class="captcha captcha-lg" data-captcha title="刷新验证"
                         onclick="this.src=window.__msRoot+'register/captcha?'+Math.random()"
                         src="{{$__msRoot}}register/captcha?{{time()}}" />
                </div>
            </div>
            <div class="help">
                <text class="ub-text-muted" data-captcha-status="tip"><i class="iconfont icon-warning"></i> 输入图片验证码验证</text>
                <text class="ub-text-muted" data-captcha-status="loading" style="display:none;"><i class="iconfont icon-refresh"></i> 正在验证</text>
                <text class="ub-text-success" data-captcha-status="success" style="display:none;"><i class="iconfont icon-checked"></i> 验证通过</text>
                <text class="ub-text-danger" data-captcha-status="error" style="display:none;"><i class="iconfont icon-close-o"></i> 验证失败</text>
            </div>
        </div>
    </div>
@endif
