$(function () {

    $(document).on('click', '[data-topic-follow-item] [data-action]', function () {
        const $btn = $(this)
        const $box = $btn.closest('[data-topic-follow-item]')
        const action = $btn.attr('data-action')
        const alias = $box.attr('data-alias')
        window.api.base.postSuccess(window.__msRoot + 'api/topic/' + action, {alias: alias}, function (res) {
            switch (action) {
                case 'follow':
                    $box.attr('data-status', 'is_follow')
                    break
                case 'unfollow':
                    $box.attr('data-status', 'not_follow')
                    break
            }
        })
        return false
    });

    $(document).on('click', '[data-post-like-item] [data-action]', function () {
        const $btn = $(this)
        const $box = $btn.closest('[data-post-like-item]')
        const action = $btn.attr('data-action')
        const alias = $box.attr('data-alias')
        window.api.base.postSuccess(window.__msRoot + 'api/post/'+action, {alias: alias}, function (res) {
            switch (action) {
                case 'like':
                    $box.attr('data-status', 'is_like')
                    break
                case 'unlike':
                    $box.attr('data-status', 'not_like')
                    break
            }
            $box.find('.cnt').html(res.data.count)
        })
        return false
    });

})
