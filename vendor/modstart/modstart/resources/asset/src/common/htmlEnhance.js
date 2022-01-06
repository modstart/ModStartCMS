$(function () {

    // 视频显示
    $('.ub-html').each(function (i, o) {
        var $html = $(o);
        $html.find('p > iframe').each(function (i, iframe) {
            var src = $(iframe).attr('src');
            if (!src) {
                return;
            }
            var videoList = [
                'v.qq.com',
                'ixigua.com',
                'player.youku.com',
                'player.bilibili.com',
            ];
            for (var i = 0; i < videoList.length; i++) {
                if (src.indexOf(videoList[i]) >= 0) {
                    $(iframe).parent().addClass('video-player');
                    return;
                }
            }
        });
    });

});