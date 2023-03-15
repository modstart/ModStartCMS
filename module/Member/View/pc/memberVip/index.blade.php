@extends($_viewFrame)

@section('pageTitleMain')开通VIP会员@endsection
@section('pageKeywords')开通VIP会员@endsection
@section('pageDescription')开通VIP会员@endsection

{!! \ModStart\ModStart::css('vendor/Member/style/member.css') !!}
@section('bodyAppend')
    @parent
    <script>
        $(function () {
            var $container = $('.vip-list-container');
            var $items = $('.pb-member-vip .vip-list .item');
            var $contents = $('.vip-content-list .item');
            $items.on('click', function () {
                var index = $items.index($(this));
                $contents.hide().eq(index).show();
                $('[data-vip-info]').find('[data-vip-value]').html('-')
                $('[data-vip-info]').show();
                $items.removeClass('active');
                $(this).addClass('active');
                $('[name=vipId]').val($(this).attr('data-vip-id'));
                @if(\Module\Member\Auth\MemberUser::isLogin())
                MS.api.post(window.__msRoot + 'api/member_vip/calc', {vipId: $(this).attr('data-vip-id')}, function (res) {
                    $('[data-vip-type]').html(res.data.type);
                    $('[data-vip-price]').html(res.data.price);
                    $('[data-vip-expire]').html(res.data.expire);
                    $('[data-vip-info]').show();
                });
                @endif
            });
            $container.find('.nav').on('click', function () {
                var currentItemIndex = $items.index($items.filter('.active'));
                if ($(this).hasClass('left')) {
                    currentItemIndex--;
                } else {
                    currentItemIndex++;
                }
                currentItemIndex = Math.max(0, Math.min(currentItemIndex, $items.length - 1));
                var $current = $items.eq(currentItemIndex).click();
                try {
                    $current.get(0).scrollIntoView({
                        behavior: 'smooth', block: 'nearest', inline: 'start'
                    });
                } catch (e) {
                }
                return false;
            });
            $($items.get(0)).click();
        });
    </script>
@endsection

@section('bodyContent')

    <div class="ub-cover tw-text-center tw-py-10"
         style="color:#FECA95;background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix('vendor/Member/image/vip_bg.jpeg')}});">
        <div class="tw-text-xl lg:tw-text-4xl">
            {{modstart_config('Member_VipTitle','开通尊贵VIP 享受更多权益')}}
        </div>
        <div class="tw-mt-4 lg:tw-text-lg">
            {{modstart_config('Member_VipSubTitle','会员权益1 丨 会员权益2 丨 会员权益3 丨 会员权益4')}}
        </div>
    </div>

    <div class="ub-container margin-top">

        <div class="pb-page-member-vip">
            <div class="top">
                <div>
                    <div class="member-info tw-flex tw-items-center">
                        <div class="tw-w-12 tw-mr-3">
                            <div class="ub-cover-1-1 tw-w-12 tw-rounded-full"
                                 style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($_memberUser?$_memberUser['avatar']:'asset/image/avatar.svg')}});"></div>
                        </div>
                        <div class="">
                            <div class="">
                                {{$_memberUser?\Module\Member\Util\MemberUtil::viewName($_memberUser):'未登录'}}
                            </div>
                            <div class="tw-mt-1">
                                @if(empty($_memberUser))
                                    <a href="{{modstart_web_url('login',['redirect'=>\ModStart\Core\Input\Request::currentPageUrl()])}}"
                                       class="vip-button">
                                        注册/登录
                                    </a>
                                @else
                                    <a href="{{modstart_web_url('member')}}"
                                       class="vip-button">
                                        用户中心
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="body">
                <div class="pb-member-vip">
                    <form action="{{$__msRoot}}api/member_vip/buy" method="post" data-ajax-form>
                        @if(\Module\Member\Auth\MemberUser::isLogin())
                            <div class="tw-mb-4 tw-py-4 tw-rounded tw-text-xl ub-text-center">
                                @if(!empty(\Module\Member\Auth\MemberVip::get()))
                                    您当前是
                                    <span
                                        class="vip-text ub-text-bold">{{\Module\Member\Auth\MemberVip::get('title')}}</span>
                                    @if(!\Module\Member\Auth\MemberVip::isDefault())
                                        ，
                                        过期时间为：{{\Module\Member\Auth\MemberUser::get('vipExpire')}}
                                    @endif
                                @endif
                            </div>
                        @endif
                        <div class="vip-list-container-box">
                            <div class="vip-list-container">
                                <div class="vip-list vip-bg tw-rounded">
                                    @foreach($memberVips as $memberVip)
                                        @if(!$memberVip['isDefault'] && $memberVip['visible'])
                                            <div class="item" data-vip-id="{{$memberVip['id']}}">
                                                <div class="tw-text-xl tw-font-bold tw-py-4">
                                                    {{$memberVip['title']}}
                                                </div>
                                                <div class="tw-text-lg tw-font-bold tw-py-4">
                                                    ￥{{$memberVip['price']}}
                                                </div>
                                                <div>
                                                    {{$memberVip['desc']?$memberVip['desc']:'[会员简要说明]'}}
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <a href="javascript:;" class="nav left">
                                    <i class="iconfont icon-angle-left"></i>
                                </a>
                                <a href="javascript:;" class="nav right">
                                    <i class="iconfont icon-angle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="tw-rounded vip-bg margin-top vip-content-list">
                            @foreach($memberVips as $memberVip)
                                @if(!$memberVip['isDefault'] && $memberVip['visible'])
                                    <div class="item tw-hidden">
                                        <div class="tw-p-4 tw-text-lg vip-text">{{$memberVip['title']}}</div>
                                        <div class="tw-pb-4 tw-px-4 ">
                                            <div class="ub-html">
                                                {!! $memberVip['content'] !!}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <div class="ub-text-center tw-py-4 vip-bg tw-rounded margin-top">
                            @if(\Module\Member\Auth\MemberUser::isLogin())
                                <input type="hidden" name="vipId" value="0"/>
                                <input type="hidden" name="redirect" value="{{\ModStart\Core\Input\Request::currentPageUrl()}}"/>
                                <div class="pay-price tw-hidden" data-vip-info>
                                    <span class="ub-text-warning" data-vip-value data-vip-type>-</span>
                                    需要支付
                                    <span class="ub-text-warning">￥</span>
                                    <span class="ub-text-warning" data-vip-value data-vip-price>-</span>
                                    购买后 <span class="ub-text-warning" data-vip-value data-vip-expire>-</span> 过期
                                </div>
                                <div class="pay-submit tw-pt-4">
                                    <button type="submit" class="lg vip-button" data-pay-submit>
                                        <i class="iconfont icon-cart"></i>
                                        立即开通
                                    </button>
                                </div>
                            @else
                                <div class="">
                                    <a class="lg vip-button" href="{{modstart_web_url('login',['redirect'=>\ModStart\Core\Input\Request::currentPageUrl()])}}">
                                        <i class="iconfont icon-cart"></i>
                                        登录后开通
                                    </a>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="ub-panel margin-top">
            <div class="head">
                <div class="title">
                    <i class="iconfont icon-description ub-color-vip"></i>
                    VIP开通说明
                </div>
            </div>
            <div class="body">
                <div class="ub-html">
                    {!! modstart_config('Member_VipContent','VIP开通说明') !!}
                </div>
            </div>
        </div>

    </div>


@endsection
