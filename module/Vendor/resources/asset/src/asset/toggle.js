$(function () {
    $(document).on('click', '[data-toggle-group] [data-action]', function () {
        const $btn = $(this)
        const $box = $btn.closest('[data-toggle-group]')
        const action = $btn.attr('data-action')
        const id = $box.attr('data-id')
        const url = $box.attr('data-toggle-group')
        window.MS.api.post(url, {
            action: action,
            id: id
        }, function (res) {
            window.MS.api.defaultCallback(res, {
                success: function (res) {
                    switch (action) {
                        case 'toggle':
                            $box.attr('data-status', 'is_toggle')
                            break
                        case 'untoggle':
                            $box.attr('data-status', 'not_toggle')
                            break
                    }
                    if (res.data && res.data.update) {
                        for (var k in res.data.update) {
                            $('[data-' + k + ']').html(res.data.update[k])
                        }
                    }
                }
            })
        })
        return false
    })
})
