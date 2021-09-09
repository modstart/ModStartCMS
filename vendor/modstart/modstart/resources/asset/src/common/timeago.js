const TimeAgo = require('./../lib/jqueryTimeago.js')

window.api.timeago = function () {
    $(function () {
        $.timeago.settings.allowFuture = true;
        $("[datetime]").timeago();

    });
}

window.api.timeago()
