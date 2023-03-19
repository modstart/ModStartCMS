<div>
    <input type="hidden" name="captchaKey"/>
    <div class="pb-captcha-box"></div>
</div>
<script src="https://api.tecmz.com/lib/captcha/base-1.0.0.js?20200410"></script>
<script>
    $(function () {
        window.tsCaptcha = window.TSCaptcha.init({
            server: "{{modstart_web_url('captcha_tecmz/verify')}}",
            selector: '.pb-captcha-box',
            appId: "{{modstart_config('CaptchaTecmz_AppId')}}",
            onValidate: function (key) {
                $('[name=captchaKey]').val(key);
                @if(!empty($param['onValidate']))
                    {!! $param['onValidate'] !!}({captchaKey:key});
                @endif
            },
            onReset:function () {
                $('[name=captchaKey]').val('');
            }
        });
        window.addEventListener('modstart:captcha.error',function(e){
            window.tsCaptcha.reset();
        });
    });
</script>
