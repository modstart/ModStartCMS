<div class="line" id="{{$id}}">
    <div class="label">
        {!! str_contains($rules,'required')?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        {{$label}}:
    </div>
    <div class="field">
        <input type="hidden"
               {{$readonly?'readonly':''}}
               class="form"
               name="{{$name}}"
               placeholder="{{$placeholder}}"
               value="{{json_encode($value)}}"/>
        <div class="ub-images-selector">
        </div>
        <div id="{{$id}}Uploader" style="width:9.4rem;border:1px solid #EEE;border-radius:0.2rem;min-height:1.6rem;"></div>
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
        var previews = [];
        try {
            images = JSON.parse($input.val());
        } catch (e) {
        }
        if(!images){
            images = [];
        }
        previews = JSON.parse(JSON.stringify(images));
        var render = function(){
            $selector.html('');
            var templateHtml = $field.find('[data-item-template]').html();
            var $item;
            for(var i =0;i<previews.length;i++){
                $item = $('<div class="item" data-index="'+i+'">' +
                    '            <div class="tools">' +
                    '                <a href="javascript:;" class="action close" data-close><i class="iconfont icon-close"></i></a>' +
                    '                <a href="javascript:;" class="action preview" data-preview data-image-preview="'+previews[i]+'"><i class="iconfont icon-eye"></i></a>' +
                    '            </div>' +
                    '            <div class="cover ub-cover-1-1" style="background-image:url('+previews[i]+');"></div>' +
                    '        </div>');
                $selector.append($item);
            }
            $input.val(JSON.stringify(images));
        };
        render();
        $selector.on('click','[data-close]',function(){
            var index = parseInt($(this).closest('[data-index]').attr('data-index'));
            images.splice(index,1);
            previews.splice(index,1);
            render();
            return false;
        });
        window.api.uploadButton('#{{$id}}Uploader', {
            text: '<div style="width:100%;box-sizing:border-box;line-height:1.5rem;height:1.5rem;padding:0;color:#666;background:#FFF;"><span class="iconfont icon-plus" style="display:inline;line-height:1.5rem;height:1.5rem;"></span> 上传</div>',
            swf: "@asset('asset/vendor/webuploader/Uploader.swf')",
            server: "{{$server}}",
            extensions: window.__dataConfig.category.image.extensions.join(','),
            sizeLimit: window.__dataConfig.category.image.maxSize,
            chunkSize: window.__dataConfig.chunkSize,
            callback: function (file, me) {
                images.push(file.path);
                previews.push(file.preview);
                render();
            },
            finish: function () {
            }
        });
    });
</script>
