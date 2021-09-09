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
               value="{{$value}}"/>
        <div id="{{$name}}Selector" class="ub-file-selector @if(!empty($value)) has-value @endif">
            <div class="value tw-inline-block tw-bg-gray-200 tw-rounded tw-text-center tw-px-2 btn" style="min-width:10rem;">{{$value or '[空]'}}</div>
            <div class="tools tw-inline-block">
                <a href="javascript:;" class="btn close" data-close><i class="iconfont icon-close"></i></a>
                @if(!$uploadMode)
                    <a href="javascript:;" class="btn add" data-add><i class="iconfont icon-plus"></i></a>
                @endif
            </div>
            {!! \ModStart\ModStart::style('
.ub-file-selector .tools .close{display:none;}
.ub-file-selector.has-value .tools .close{display:inline-block;}
.ub-file-selector.has-value .tools .add{display:none;}
.ub-file-selector .uploader{background:transparent;}
') !!}
            @if(in_array($uploadMode,['uploadDirectRaw','uploadDirect']))
                {!! \ModStart\ModStart::js('asset/common/uploadButton.js') !!}
                <div id="{{$id}}Uploader" class="uploader" style="width:10rem;text-align:center;"></div>
            @endif
        </div>
        <script>
            $(function () {
                var $field = $('#{{$id}}');
                var $selector = $('#{{$name}}Selector');
                function setValue(path){
                    $field.find('[name="{{$name}}"]').val(path);
                    if(path){
                        $selector.find('.value').html(path);
                        $selector.addClass('has-value');
                    }else{
                        $selector.find('.value').html('[空]');
                        $selector.removeClass('has-value');
                    }
                }
                @if(in_array($uploadMode,['uploadDirectRaw','uploadDirect']))
                    window.api.uploadButton('#{{$id}}Uploader', {
                        text: '<div style="line-height:1.5rem;height:1.5rem;padding:0;background:transparent;border-radius:0.1rem;"><span class="iconfont icon-plus" style="display:inline;line-height:1.5rem;height:1.5rem;background:transparent;"></span> {{L('Select File')}}</div>',
                        swf: "@asset('asset/vendor/webuploader/Uploader.swf')",
                        server: "{{$server}}?action={{$uploadMode}}",
                        extensions: window.__dataConfig.category.file.extensions.join(','),
                        sizeLimit: window.__dataConfig.category.file.maxSize,
                        chunkSize: window.__dataConfig.chunkSize,
                        showFileQueue: false,
                        callback: function (file, me) {
                            setValue(file.path);
                        },
                        finish: function () {
                        }
                    });
                @else
                    $selector.find('.tools .add').on('click', function () {
                        window.__selectorDialog = new window.api.selectorDialog({
                            server: '{{$server}}',
                            callback: function (items) {
                                if (items.length > 0) {
                                    setValue(items[0].path);
                                }
                            }
                        }).show();
                    });
                @endif
                $selector.find('.tools .close').on('click', function () {
                    setValue('');
                });
            });
        </script>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
