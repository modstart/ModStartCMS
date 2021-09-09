<div class="field" data-grid-filter-field="{{$id}}">
    <div class="name">{{$label}}</div>
    <div class="input">
        <div class="inputs" style="display:inline-block;"></div>
        <a href="javascript:;" class="ub-text-muted" data-like-edit style="line-height:1.5rem;"><i
                    class="iconfont icon-edit"></i></a>
    </div>
    <div style="display:none;" data-dialog-template>
        <div style="max-width:600px;">
            <div class="ub-panel">
                <div class="head">
                    <div class="title">{{L('Please Select')}}</div>
                </div>
                <div class="body">
                    <div class="content"
                         style="max-height:100px;box-shadow:0 0 5px #CCC;padding:0.5rem;border-radius:0.2rem;overflow:auto;">
                        <table>
                            @foreach($field->options() as $groupTagItem)
                                <tr>
                                    <td style="width:6em;vertical-align:top;">{{$groupTagItem['groupTitle']}}</td>
                                    <td>
                                        @foreach($groupTagItem['groupTags'] as $groupTagItemItem)
                                            <label style="min-width:6em;">
                                                @if($field->serializeType()===\ModStart\Field\Tags::SERIALIZE_TYPE_COLON_SEPARATED)
                                                    <input type="checkbox" value=":{{$groupTagItemItem['id']}}:"
                                                           title="{{$groupTagItemItem['title']}}"/>
                                                @else
                                                    <input type="checkbox" value="{{$groupTagItemItem['id']}}"
                                                           title="{{$groupTagItemItem['title']}}"/>
                                                @endif
                                                {{$groupTagItemItem['title']}}
                                            </label>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
                <div class="foot ub-text-center" style="padding:10px;">
                    <a class="btn btn-primary btn-block" href="javascript:;"
                       data-dialog-comfirm="GridFilterField_{{$id}}">{{L('Confirm')}}</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    (function () {
        var $field = $('[data-grid-filter-field={{$id}}]');
        var $dialogTemplate = $field.find('[data-dialog-template]');
        var tagTitleMap = {};
        $dialogTemplate.find('input[type=checkbox]').each(function (i, o) {
            tagTitleMap[$(o).val() + ''] = $(o).attr('title');
        });
        var dialogIndex;
        var refreshInput = function (ids) {
            $field.find('.inputs').html('');
            for (var i = 0; i < ids.length; i++) {
                if (tagTitleMap[ids[i]]) {
                    $field.find('.inputs').append('<div class="item" style="display:inline-block;margin-right:0.2rem;border:1px solid #c4cfdb;border-radius:0.2rem;padding:0 0.1rem;background:#FFF;" data-id="' + ids[i] + '">' + window.api.util.specialchars(tagTitleMap[ids[i]])
                        + '<a class="ub-text-muted" href="javascript:;" data-input-close style="width:0.8rem;font-size:0.5rem;line-height:1.3rem;text-align:center;"><i class="iconfont icon-close"></i></a></div>');
                }
            }
        };
        $field.find('[data-like-edit]').on('click', function () {
            $dialogTemplate.find('.content').css('max-height', ($(window).height() - 130) + 'px');
            dialogIndex = window.api.dialog.dialogContent($dialogTemplate.html());
        });
        $(document).on('click', '[data-dialog-comfirm="GridFilterField_{{$id}}"]', function () {
            var ids = [];
            $('#layui-layer' + dialogIndex).find('input[type=checkbox]:checked').each(function (i, o) {
                ids.push($(o).val());
            })
            $dialogTemplate.find('input[type=checkbox]').each(function (i, o) {
                if (ids.indexOf($(o).val()) >= 0) {
                    $(o).attr('checked', 'checked');
                } else {
                    $(o).removeAttr('checked');
                }
            });
            window.api.dialog.dialogClose(dialogIndex);
            refreshInput(ids);
        });
        $field.on('click', '[data-input-close]', function () {
            $(this).parent().remove();
        });
        $field.data('get', function () {
            var likes = [];
            $field.find('.inputs .item').each(function (i, o) {
                likes.push($(o).attr('data-id'));
            });
            return {
                '{{$column}}': {
                    likes: likes
                }
            };
        });
        $field.data('reset', function () {
            $field.find('.inputs').html('');
            $dialogTemplate.find('input[type=checkbox]').removeAttr('checked');
        });
        $field.data('init', function (data) {
            for (var i = 0; i < data.length; i++) {
                for (var k in data[i]) {
                    if (k === '{{$column}}' && ('likes' in data[i][k]) && Array.isArray(data[i][k]['likes'])) {
                        refreshInput(data[i][k].likes);
                    }
                }
            }
        });
    })();
</script>