{{\ModStart\ModStart::js('asset/common/commonVerify.js')}}
{{\ModStart\ModStart::js('vendor/Member/entry/register.js')}}
<script>
    $(function () {
        new window.api.commonVerify({
            generateServer: '{{$__msRoot}}register/phone_verify',
            selectorTarget: 'input[name=phone]',
            selectorGenerate: '[data-phone-verify-generate]',
            selectorCountdown: '[data-phone-verify-countdown]',
            selectorRegenerate: '[data-phone-verify-regenerate]',
            @if(!\Module\Member\Util\SecurityUtil::registerCaptchaProvider())
            selectorCaptcha: 'input[name=captcha]',
            selectorCaptchaImg:'[data-none]',
            @endif
            interval: 60
        },window.api.dialog);
    });
</script>
