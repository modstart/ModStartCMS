<div class="line" data-field id="{{$id}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        <input type="text"
               {{$readonly?'readonly':''}}
               class="form"
               name="{{$name}}"
               placeholder="{{$placeholder}}"
               style="width:50%;"
               value="{{null===$value?$defaultValue:$value}}" />
        <a href="javascript:;" class="btn" id="{{$name}}Selector"><i class="iconfont icon-list-alt"></i></a>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
<script>
    $(function () {
        var $field = $('#{{$id}}');
        var $selector = $('#{{$name}}Selector');
        $selector.on('click', function () {
            window.__selectorDialog = new window.api.selectorDialog({
                server: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($server) !!},
                callback: (items) => {
                    // console.log('doSelect', items)
                    if (items.length > 0) {
                        $field.find('[name={{$name}}]').val(items[0].link);
                    }
                }
            }).show();
        });
    });
</script>
