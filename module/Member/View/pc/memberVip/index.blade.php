@extends($_viewFrame)

@section('pageTitleMain','会员VIP')

{!! \ModStart\ModStart::css('vendor/Member/style/member.css') !!}
@section('bodyAppend')
    @parent
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
@endsection

@section('bodyContent')

    <div class="ub-container">

        <div class="ub-breadcrumb">
            <a href="{{$__msRoot}}">首页</a>
            <a href="{{$__msRoot}}member_vip">开通会员</a>
        </div>

        <div class="ub-panel">
            <div class="head">
                <div class="title">开通/续费会员</div>
            </div>
            <div class="body pb-member-vip">
                <form action="{{$__msRoot}}api/member_vip/buy" method="post" data-ajax-form>
                    <div class="vip-current">
                        @if(!empty(\Module\Member\Auth\MemberVip::get()))
                            您当前是
                            <span class="ub-color-vip ub-text-bold">{{\Module\Member\Auth\MemberVip::get('title')}}</span>
                            ，
                            过期时间为：{{\Module\Member\Auth\MemberUser::get('vipExpire')}}
                        @endif
                    </div>
                    <div class="vip-list">
                        <div class="row">
                            @foreach($memberVips as $memberVip)
                                @if(!$memberVip['isDefault'])
                                    <div class="col-md-3">
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
                    <input type="hidden" name="vipId" value="0" />
                    <input type="hidden" name="redirect" value="{{\ModStart\Core\Input\Request::currentPageUrl()}}" />
                    <div class="pay-price" data-vip-info style="display:none;">
                        <span class="ub-text-warning" data-vip-value data-vip-type>-</span>
                        需要支付 <span class="ub-text-warning">￥</span><span class="ub-text-warning" data-vip-value data-vip-price>-</span>
                        ，
                        购买后<span class="ub-text-warning" data-vip-value data-vip-expire>-</span>过期
                    </div>
                    <div class="pay-submit">
                        <button type="submit" class="btn btn-lg btn-primary" data-pay-submit>提交支付</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection
