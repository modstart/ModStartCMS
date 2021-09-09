MS.ready(
    function () {
        return window.__grids;
    },
    function () {
        var grid = window.__grids.get(0);
        grid.$lister.on('click', '[data-batch-read-all]', function () {
            window.api.base.post('member_message/read_all', {_id: grid.getCheckedIds()}, function (res) {
                grid.lister.refresh();
            });
            grid.lister.refresh();
        });
        grid.$lister.on('click', '[data-item-read]', function () {
            window.api.base.post('member_message/read', {_id: grid.getId(this)}, function (res) {
                grid.lister.refresh();
            });
        });
        grid.$lister.on('click', '[data-batch-item-read]', function () {
            var ids = grid.getCheckedIds();
            if (!ids.length) {
                window.api.dialog.tipError('请选择消息');
                return;
            }
            window.api.base.post('member_message/read', {_id: ids.join(',')}, function (res) {
                grid.lister.refresh();
            });
        });
    }
);
