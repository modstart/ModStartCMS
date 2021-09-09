const TimeAgo = require('./../vendor/jqueryLazyload.js')

window.api.lazyLoad = function () {
    $(function () {
        $('[data-src]').lazyload({data_attribute: 'src'})
    })
}

window.api.lazyLoad()
