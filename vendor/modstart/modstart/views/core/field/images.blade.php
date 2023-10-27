<div class="line" id="{{$id}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        <input type="hidden"
               {{$readonly?'readonly':''}}
               class="form"
               name="{{$name}}"
               placeholder="{{$placeholder}}"
               value="{{\ModStart\Core\Util\SerializeUtil::jsonEncode(null===$value?(null===$defaultValue?[]:$defaultValue):$value)}}"/>
        <div class="ub-images-selector">
            <div class="item">
                <div class="tools">
                    <a href="javascript:;" class="action close" data-close><i class="iconfont icon-close"></i></a>
                    <a href="javascript:;" class="action preview" data-preview><i class="iconfont icon-eye"></i></a>
                </div>
                <div class="cover ub-cover-1-1 contain" data-background-src="placeholder/500x500"></div>
            </div>
            <div class="item">
                <div class="tools">
                    <a href="javascript:;" class="action close" data-close><i class="iconfont icon-close"></i></a>
                    <a href="javascript:;" class="action preview" data-preview><i class="iconfont icon-eye"></i></a>
                </div>
                <div class="cover ub-cover-1-1 contain" data-background-src="placeholder/500x500"></div>
            </div>
            <div class="item add">
                <a href="javascript:;" class="action add" data-add><i class="iconfont icon-plus"></i></a>
            </div>
        </div>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
<script>
    $(function () {
        var $field = $('#{{$id}}');
        var $input = $field.find('input');
        var $selector = $field.find('.ub-images-selector');
        var images = [];
        try {
            images = JSON.parse($input.val());
        } catch (e) {
        }
        if(!images){
            images = [];
        }
        var render = function(){
            $selector.html('');
            var templateHtml = $field.find('[data-item-template]').html();
            var $item;
            for(var i =0;i<images.length;i++){
                $item = $('<div class="item" data-index="'+i+'">' +
                    '            <div class="tools">' +
                    '                <a href="javascript:;" class="action close" data-close><i class="iconfont icon-close"></i></a>' +
                    '                <a href="javascript:;" class="action preview" data-preview data-image-preview="'+images[i]+'"><i class="iconfont icon-eye"></i></a>' +
                    '            </div>' +
                    '            <div class="cover ub-cover-1-1 contain" style="background-image:url('+images[i]+');"></div>' +
                    '        </div>');
                $selector.append($item);
            }
            $selector.append('<div class="item add">' +
                '                <a href="javascript:;" class="action add" data-add><i class="iconfont icon-plus"></i></a>' +
                '            </div>');
            $input.val(JSON.stringify(images));
        };
        render();
        $selector.on('click','[data-add]',function(){
            window.__selectorDialog = new window.api.selectorDialog({
                server: '{{$server}}',
                callback: function (items) {
                    items.forEach(o=>{
                        images.push(o.fullPath);
                    })
                    render();
                }
            }).show();
            return false;
        });
        $selector.on('click','[data-close]',function(){
            var index = parseInt($(this).closest('[data-index]').attr('data-index'));
            images.splice(index,1);
            render();
            return false;
        });
    });
</script>
