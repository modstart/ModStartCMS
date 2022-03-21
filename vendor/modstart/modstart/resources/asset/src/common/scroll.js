window.MS = window.MS || {}
window.MS.scroll = {
    board: function (container, option) {
        var opt = $.extend({
            itemSelecor: '.item',
            itemHeight: null,
            interval: 3000,
        }, option);
        var $container = $(container);
        var isOver = false;
        if (null === opt.itemHeight) {
            opt.itemHeight = $container.find(opt.itemSelecor).eq(0).height();
        }
        var $itemContainer = $container.find(opt.itemSelecor).parent();
        $itemContainer.append($container.find(opt.itemSelecor).eq(0).clone());
        $itemContainer
            .on('mouseover', function () {
                isOver = true;
            })
            .on('mouseout', function () {
                isOver = false;
            });
        var $items = $container.find(opt.itemSelecor);
        var i = 0;
        setInterval(function () {
            if (isOver) {
                return;
            }
            i++;
            if (i >= $items.length) {
                $itemContainer.css({marginTop: '0'});
                i = 1;
            }
            $itemContainer.animate({marginTop: '-' + (opt.itemHeight * i) + 'px'});
        }, opt.interval);
    }
};
