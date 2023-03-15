const LazyLoad = require('./../vendor/jqueryLazyload.js')

window.api.lazyLoad = function () {
    $(function () {
        $('[data-src]').lazyload({
            data_attribute: 'src',
        });
    })
}

window.api.lazyLoad()

window.MS.lazyload = {
    init: function () {
        window.api.lazyLoad()
    }
}
