<div class="line" data-field="{{$name}}" id="{{$id}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        @if(modstart_module_enabled('Area'))
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
        @else
            <div class="ub-alert">
                请安装 <a href="https://modstart.com/m/Area" target="_blank">Area</a> 模块支持
            </div>
        @endif
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
@if(modstart_module_enabled('Area'))
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
@endif
