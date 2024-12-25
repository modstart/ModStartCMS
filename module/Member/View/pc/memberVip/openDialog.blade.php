<script>
    window.__openVip = function (vipId) {
        if (window.__voucherSelect) {
            window.__voucherSelect.registerOnSelected(function (value) {
                window.__refreshMemberVipPay(vipId);
            });
            window.__voucherSelect.updateItems('MemberVip', {
                vipId: vipId
            }, function () {
                window.__refreshMemberVipPay(vipId)
            });
        } else {
            window.__refreshMemberVipPay(vipId)
        }
        $('[data-open-dialog]').addClass('ub-open');
    };
    window.__closeVip = function(){
        $('[data-open-dialog]').removeClass('ub-open');
        window.__payCenterQuick.close();
    };
    window.__refreshMemberVipPay = function (vipId) {
        window.__payCenterQuick.empty();
        window.__payCenterQuick.registerUnsupported(function (type) {
            $('[name=vipId]').val(vipId);
            if (window.__voucherSelect) {
                $('[name=voucherId]').val(window.__voucherSelect.getSelected());
            }
            $('[data-ajax-form]').submit();
        });
        let calcData = {}
        calcData.vipId = vipId;
        if (window.__voucherSelect) {
            calcData.voucherId = window.__voucherSelect.getSelected();
        }
        MS.api.post("{{modstart_api_url('member_vip/calc')}}", calcData, function (res) {
            $('[data-vip-type]').html(res.data.type);
            $('[data-vip-price]').html(res.data.price);
            $('[data-vip-expire]').html(res.data.expire);
            $('[data-vip-info]').show();
            window.__payCenterQuick.prepareLazy(
                '{{\Module\Member\Core\MemberVipPayCenterBiz::NAME}}',
                {money: res.data.price},
                calcData
            );
        });
    };
</script>
<div class="ub-modal" data-open-dialog aria-hidden="false">
    <div class="ub-modal-dialog tw-top-10"
         style="background-image: linear-gradient(180deg, #ffe1b2, #fff9ed);">
        <a href="javascript:" class="ub-modal-close"
           onclick="window.__closeVip();">
            <i class="icon-close iconfont tw-text-lx tw-text-yellow-800"></i>
        </a>
        <div class="ub-modal-body">
            <div class="tw-px-4 tw-pt-2">
                <div class="margin-bottom tw-flex tw--mt-2 tw-text-xl tw-text-yellow-800">
                    <div class="tw-flex-grow">
                        开通VIP
                    </div>
                    <div class="ub-text-sm">
                        <div class="ub-text-danger">
                            限时优惠剩余时间：<span data-count-down></span>
                        </div>
                    </div>
                </div>
                <div class="">
                    @include('module::PayCenter.View.inc.quick')
                </div>
                @if(modstart_module_enabled('Voucher'))
                    <div class="margin-bottom tw-p-3 tw-rounded-lg ub-border ub-content-bg">
                        {!! \Module\Voucher\Render\VoucherRender::select() !!}
                    </div>
                @endif
                <div class="margin-bottom tw-p-3 tw-rounded-lg ub-border ub-content-bg" data-vip-info>
                    <span class="ub-text-warning" data-vip-value data-vip-type>-</span>
                    需要支付
                    <span class="ub-text-warning">￥</span><span class="ub-text-warning" data-vip-value
                                                                data-vip-price>-</span>
                    购买后 <span class="ub-text-warning" data-vip-value data-vip-expire>-</span> 过期
                </div>
                <div>
                    开通即表示同意
                    @if(modstart_config('Member_AgreementEnable',false))
                        <a href="{{modstart_web_url('member/agreement')}}"
                           class="ub-text-tertiary"
                           target="_blank">《{{modstart_config('Member_AgreementTitle','用户使用协议')}}》</a>
                    @endif
                    @if(modstart_config('Member_PrivacyEnable',false))
                        <a href="{{modstart_web_url('member/privacy')}}"
                           class="ub-text-tertiary"
                           target="_blank">《{{modstart_config('Member_PrivacyTitle','用户隐私协议')}}》</a>
                    @endif
                    <a href="{{modstart_web_url('member/vip')}}"
                       class="ub-text-tertiary"
                       target="_blank">《{{modstart_config('Member_VipTitle','会员协议')}}》</a>
                </div>
            </div>
        </div>
    </div>
</div>
