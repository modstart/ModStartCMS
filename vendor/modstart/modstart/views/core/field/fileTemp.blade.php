<div class="line" data-field id="{{$id}}">
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
               data-value-holder
               name="{{$name}}"
               placeholder="{{$placeholder}}"
               value="{{null===$value?$defaultValue:$value}}"/>
        <div class="ub-file-selector" style="border:1px solid #EEE;position:relative;margin-bottom:0.5rem;border-radius:0.2rem;padding:0 2rem 0 0.5rem;display:inline-block;">
            <div data-value></div>
            <a data-close href="javascript:;" style="position:absolute;right:0px;top:0px;display:inline-block;line-height:1.5rem;width:1rem;text-align:center;color:#999;"><i class="iconfont icon-close"></i></a>
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
        var $selector = $field.find('.ub-file-selector');
        var fileValue = $input.val();
        var fileName = fileValue;
        var render = function(){
            if(!fileValue){
                $selector.hide();
                return;
            }
            $selector.find('[data-value]').html(fileName);
            $field.find('[data-value-holder]').val(fileValue);
            $selector.show();
        };
        render();
        $selector.on('click','[data-close]',function(){
            fileValue = '';
            fileName = '';
            render();
            return false;
        });
        window.api.uploadButton('#{{$id}}Uploader', {
            text: '<div style="width:100%;box-sizing:border-box;line-height:1.5rem;height:1.5rem;padding:0;color:#666;background:#FFF;"><span class="iconfont icon-plus" style="display:inline;line-height:1.5rem;height:1.5rem;"></span> 上传</div>',
            server: "{{$server}}",
            extensions: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(join(',',config('data.upload.file.extensions'))) !!},
            sizeLimit: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(config('data.upload.file.maxSize')) !!},
            chunkSize: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(\ModStart\Core\Util\EnvUtil::env('uploadMaxSize')) !!},
            callback: function (file, me) {
                // console.log('file',file);
                fileValue = file.path;
                fileName = file.name;
                render();
            },
            finish: function () {
            }
        });
    });
</script>
