<div class="field" data-grid-filter-field="{{$id}}">
    <div class="name">{{$label}}</div>
    <div class="input">
        <div class="inputs" style="display:inline-block;"></div>
        <a href="javascript:;" class="ub-text-muted" data-like-add style="line-height:1.5rem;"><i class="iconfont icon-plus"></i></a>
    </div>
    <div data-input-template style="display:none;">
        <div style="display:inline-block;position:relative;">
            <a href="javascript:;" class="ub-text-muted" data-input-close style="position:absolute;width:0.8rem;font-size:0.5rem;line-height:1.3rem;text-align:center;top:0;right:0;">
                <i class="iconfont icon-close"></i>
            </a>
            <input type="text" class="form"/>
        </div>
    </div>
</div>
<script>
    (function () {
        var $field = $('[data-grid-filter-field={{$id}}]');
        var inputHtml = $field.find('[data-input-template]').html()
        $field.find('[data-like-add]').on('click', function () {
            $field.find('.inputs').append(inputHtml);
        });
        $field.on('click','[data-input-close]',function(){
            $(this).parent().remove();
        });
        $field.data('get', function () {
            var likes = [];
            $field.find('.inputs input').each(function(i,o){
                likes.push($(o).val());
            });
            return {
                '{{$column}}': {
                    likes: likes
                }
            };
        });
        $field.data('reset', function () {
            $field.find('.inputs').html('');
        });
        $field.data('init', function (data) {
            for (var i = 0; i < data.length; i++) {
                for (var k in data[i]) {
                    if (k === '{{$column}}' && ('likes' in data[i][k]) && Array.isArray(data[i][k]['likes'])) {
                        for (var j = 0; j < data[i][k].likes.length; j++) {
                            $field.find('.inputs').append(inputHtml);
                            $($field.find('.inputs input').get(j)).val(data[i][k].likes[j]);
                        }
                    }
                }
            }
        });
    })();
</script>
