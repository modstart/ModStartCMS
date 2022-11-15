const TimeAgo = require('./../lib/jqueryTimeago.js')

if (!('api' in window)) {
    window.api = {}
}

window.api.timeago = function () {
    $(function () {
        $.timeago.settings.allowFuture = true;
        $("[datetime]").timeago();

    });
}

window.api.timeago()

if (!('MS' in window)) {
    window.MS = {}
}
window.MS.timeago = window.api.timeago
