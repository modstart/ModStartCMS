$(function () {
    var Dialog = window.api.dialog;
    var Base = window.api.base;
    var AdminUrl = window.__msAdminRoot;
    if ($('[data-admin-version]').length > 0) {
        $(document).on('click', '[data-admin-upgrade]', function () {
            Dialog.confirm("请在升级之前做好系统备份，确定升级？", function () {
                Dialog.confirm("再次确定已经做好数据备份，确定升级？", function () {
                    Dialog.confirm("升级需要最长可能10分钟的时间，请在升级过程中不要关闭浏览器", function () {
                        request('init');
                    });
                });
            });
            return false;
        });
        var request = function (action) {
            Dialog.loadingOn();
            Base.post(AdminUrl + 'upgrade/' + action, {}, function (res) {
                Dialog.loadingOff();
                Base.defaultFormCallback(res, {
                    success: function (res) {
                        if (res.data.action === 'finish') {
                            Dialog.alertSuccess('\u5347\u7ea7\u5b8c\u6210', function () {
                                window.location.href = AdminUrl + 'logout';
                            });
                        } else {
                            Dialog.tipSuccess(res.data.msg);
                            setTimeout(function () {
                                request(res.data.action);
                            }, 2000);
                        }
                    }
                });
            });
        };
        var version = $('[data-admin-version]').attr('data-admin-version');
        $('body').append('<script src="https://www.tecmz.com/product/' + window.__version_check_app + '/version_check?domain=' + window.location.host + '&version=' + version + '"></script>');
    }
});
