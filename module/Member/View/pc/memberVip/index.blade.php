@extends($_viewFrame)

@section('pageTitleMain')开通VIP会员@endsection
@section('pageKeywords')开通VIP会员@endsection
@section('pageDescription')开通VIP会员@endsection

{!! \ModStart\ModStart::css('vendor/Member/style/member.css') !!}
@section('bodyAppend')
    @parent
    @if(\Module\Member\Auth\MemberUser::isLogin())
        <script>
            $(function () {
                var $items = $('.pb-member-vip .vip-list .item');
                $items.on('click', function () {
                    $('[data-vip-info]').find('[data-vip-value]').html('-')
                    $('[data-vip-info]').show();
                    $items.removeClass('active');
                    $(this).addClass('active');
                    $('[name=vipId]').val($(this).attr('data-vip-id'));
                    window.api.base.post(window.__msRoot+'api/member_vip/calc',{vipId:$(this).attr('data-vip-id')},function (res) {
                        $('[data-vip-type]').html(res.data.type);
                        $('[data-vip-price]').html(res.data.price);
                        $('[data-vip-expire]').html(res.data.expire);
                        $('[data-vip-info]').show();
                    });
                    return false;
                });
                $($items.get(0)).click();
            });
        </script>
    @endif
@endsection

@section('bodyContent')

    <div class="ub-container">

        <div class="ub-breadcrumb">
            <a href="{{$__msRoot}}">首页</a>
            <a href="{{$__msRoot}}member_vip">开通VIP会员</a>
        </div>

        <div class="tw-rounded ub-cover tw-text-center tw-py-10" style="color:#f2e6b9;background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix('vendor/Member/image/vip_bg.jpeg')}});">
            <div class="tw-text-xl lg:tw-text-4xl">
                {{modstart_config('Member_VipTitle','开通尊贵VIP 享受更多权益')}}
            </div>
            <div class="tw-mt-4">
                {{modstart_config('Member_VipSubTitle','会员权益1 丨 会员权益2 丨 会员权益3 丨 会员权益4')}}
            </div>
        </div>

        <div class="ub-panel">
            <div class="head"></div>
            <div class="body pb-member-vip">
                <form action="{{$__msRoot}}api/member_vip/buy" method="post" data-ajax-form>
                    @if(\Module\Member\Auth\MemberUser::isLogin())
                        <div class="vip-current ub-text-center">
                            @if(!empty(\Module\Member\Auth\MemberVip::get()))
                                您当前是
                                <span class="ub-color-vip ub-text-bold">{{\Module\Member\Auth\MemberVip::get('title')}}</span>
                                @if(!\Module\Member\Auth\MemberVip::isDefault())
                                    ，
                                    过期时间为：{{\Module\Member\Auth\MemberUser::get('vipExpire')}}
                                @endif
                            @endif
                        </div>
                    @endif
                    <div class="vip-list">
                        <div class="row">
                            @foreach($memberVips as $memberVip)
                                @if(!$memberVip['isDefault'] && $memberVip['visible'])
                                    <div class="col-md-4">
                                        <div class="item" data-vip-id="{{$memberVip['id']}}">
                                            <div class="title">
                                                {{$memberVip['title']}}
                                            </div>
                                            <div class="price">
                                                ￥{{$memberVip['price']}}
                                            </div>
                                            <div class="content">
                                                {!! $memberVip['content'] !!}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="ub-text-center">
                        @if(\Module\Member\Auth\MemberUser::isLogin())
                            <input type="hidden" name="vipId" value="0" />
                            <input type="hidden" name="redirect" value="{{\ModStart\Core\Input\Request::currentPageUrl()}}" />
                            <div class="pay-price" data-vip-info style="display:none;">
                                <span class="ub-text-warning" data-vip-value data-vip-type>-</span>
                                需要支付 <span class="ub-text-warning">￥</span><span class="ub-text-warning" data-vip-value data-vip-price>-</span>
                                ，
                                购买后<span class="ub-text-warning" data-vip-value data-vip-expire>-</span>过期
                            </div>
                            <div class="pay-submit">
                                <button type="submit" class="btn btn-lg btn-primary" data-pay-submit>
                                    <i class="iconfont icon-cart"></i>
                                    提交支付
                                </button>
                            </div>
                        @else
                            <div class="pay-submit">
                                <a class="btn btn-lg btn-primary"
                                   href="{{modstart_web_url('login',['redirect'=>\ModStart\Core\Input\Request::currentPageUrl()])}}">
                                    登录开通会员
                                </a>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection
