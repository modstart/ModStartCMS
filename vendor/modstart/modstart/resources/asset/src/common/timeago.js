const TimeAgo = require('./../lib/jquery.timeago.js')
const TImeAgoLang = require('./../lib/jquery.timeago.zh-CN.js')

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
