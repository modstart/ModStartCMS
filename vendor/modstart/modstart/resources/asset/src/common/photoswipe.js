var PhotoSwipe = require('./../vendor/photoswipe/photoswipe.js');
var PhotoSwipeUI = require('./../vendor/photoswipe/photoswipe-ui-default.js');
var PhotoSwipeCss = require('./../vendor/photoswipe/photoswipe.css');
var PhotoSwipeSkinCss = require('./../vendor/photoswipe/default-skin/default-skin.css');
var TemplateHtml = require('./../vendor/photoswipe/template.html');


if (!('api' in window)) {
    window.api = {}
}
window.api.photoswipe = function (container, option) {

    if (!$('[data-photoswipe-container]').length) {
        $('body').append(TemplateHtml);
    }

    var opt = $.extend({
        onTail: function (gallery) {

        }
    }, option);
    var $container = $(container);

    $container.on('click', '[data-photoswipe-item]', function () {
        var $me = $(this);
        var loadItems = function () {
            var items = [];
            $container.find('[data-photoswipe-item]').each(function (i, o) {
                var $o = $(o);
                var item = {};
                item.src = $o.attr('data-src');
                item.w = parseInt($o.attr('data-width'));
                item.h = parseInt($o.attr('data-height'));
                items.push(item);
            });
            return items;
        };
        var index = $container.find('[data-photoswipe-item]').index($me);
        // console.log('data-photoswipe-item', index, items);
        var options = {
            index: index,
            escKey: false,
            timeToIdle: 4000,
            pinchToClose: false,
            closeOnScroll: false,
        };
        var ele = document.querySelector('[data-photoswipe-container]');
        var gallery = new PhotoSwipe(ele, PhotoSwipeUI, loadItems(), options);
        gallery.listen('afterChange', function () {
            if (gallery.getCurrentIndex() == gallery.items.length - 1) {
                opt.onTail(gallery);
            }
        });
        gallery.init();
    });
}

if (!('MS' in window)) {
    window.MS = {}
}
window.MS.photoswipe=window.api.photoswipe
