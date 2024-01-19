$(function () {

    // VIP 点击滚动
    var $container = $('.vip-list-container');
    var $items = $('.pb-member-vip .vip-list .item');
    var $contents = $('.vip-content-list .item');
    $items.on('click', function () {
        var vipId = $(this).attr('data-vip-id');
        var index = $items.index($(this));
        $contents.hide().eq(index).show();
        $('[data-vip-info]').find('[data-vip-value]').html('-')
        $('[data-vip-info]').show();
        $items.removeClass('active');
        $(this).addClass('active');
        $('[name=vipId]').val($(this).attr('data-vip-id'));
        var $rights = $('[data-vip-right-list] [data-vip-right]');
        $rights.hide().filter(function (i, o) {
            return $(o).attr('data-vip-right').split(',').indexOf(vipId) >= 0;
        }).show();
        if (window.__data.isLogin) {
            MS.api.post(window.__msRoot + 'api/member_vip/calc', {vipId: vipId}, function (res) {
                $('[data-vip-type]').html(res.data.type);
                $('[data-vip-price]').html(res.data.price);
                $('[data-vip-expire]').html(res.data.expire);
                $('[data-vip-info]').show();
                window.__refreshMemberVipPay && window.__refreshMemberVipPay();
            });
        }
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

    // 倒计时
    var end = new Date().getTime() + window.__data.countDownSeconds * 1000;
    var $countDown = $('[data-count-down]');
    setInterval(function () {
        var left = end - new Date().getTime();
        if (left <= 0) {
            $countDown.html('00:00:00.0');
        } else {
            var h = Math.floor(left / 1000 / 60 / 60);
            var m = Math.floor(left / 1000 / 60 % 60);
            var s = Math.floor(left / 1000 % 60);
            var ms = Math.floor(left % 1000 / 100);
            $countDown.html((h < 10 ? '0' + h : h) + ':' + (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s) + '.' + (ms < 10 ? '0' + ms : ms));
        }
    }, 100);

    // 开通列表
    var swiper = new Swiper("[data-vip-open-list]", {
        direction: "vertical",
        slidesPerView: 5,
        rewind: true,
        loop: true,
        autoplay: {
            delay: 2000,
        },
    });

});
