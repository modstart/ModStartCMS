MS.ready(
    function () {
        return window.__grids;
    },
    function () {
        var updateUnreadMessageCount = function (cnt) {
            $('[data-member-unread-message-count]').html(cnt)
            if (cnt <= 0) {
                $('[data-member-unread-message-count]').addClass('tw-animate-ping');
                setTimeout(function () {
                    $('[data-member-unread-message-count]').remove();
                }, 1000);
            }
        };
        var setAdRead = function (id) {
            MS.api.post('member_message/read', {_id: id}, function (res) {
                updateUnreadMessageCount(res.data.unreadMessageCount);
            });
        };
        var grid = window.__grids.get(0);
        grid.$lister.on('click', '[data-batch-read-all]', function () {
            var ids = $('[data-message-id]').map(function () {
                return $(this).data('message-id');
            }).get();
            MS.api.post('member_message/read_all', {_id: ids.join(',')}, function (res) {
                grid.lister.refresh();
                MS.dialog.tipSuccess('操作成功');
                updateUnreadMessageCount(0);
            });
        });
        grid.$lister.on('click', '[data-item-read]', function () {
            var $message = $(this).closest('[data-message-id]');
            setAdRead($message.data('message-id'));
            $message.find('[data-message-unread],[data-item-read]').remove();
        });
        grid.$lister.on('click', '[data-message-id]', function () {
            if ($(this).find('[data-message-unread]').length) {
                $(this).find('[data-message-unread],[data-item-read]').remove();
                setAdRead($(this).data('message-id'));
            }
        });
        grid.$lister.on('click', '[data-item-delete]', function () {
            MS.api.post('member_message/delete', {_id: $(this).closest('[data-message-id]').data('message-id')}, function (res) {
                grid.lister.refresh();
                updateUnreadMessageCount(res.data.unreadMessageCount);
            });
        });
    }
);
