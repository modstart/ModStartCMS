<div class="line" id="{{$id}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        <div class="multi-selector-container">
            <div data-select>
                <div class="ub-text-muted">{{L('Loading')}}</div>
            </div>
            <input type="hidden"
                   data-title
                   name="{{$name}}"
                   value="{{null===$value?$defaultValue:$value}}"
            />
        </div>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
<script>
    $(function () {
        window.api.base.post("{{modstart_api_url('area/china')}}",{},function(res){
            new window.api.multiSelector({
                container:'#{{$id}} .multi-selector-container',
                data:res.data
            });
        });
    });
</script>
