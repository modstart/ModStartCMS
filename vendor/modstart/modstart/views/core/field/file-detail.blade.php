<div class="line" data-field="{{$name}}">
    <div class="label">
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        <div class="value">
            @if(!empty($value))
                <a href="{{$value}}" target="_blank" class="tw-inline-block tw-px-2 tw-bg-gray-200 tw-rounded tw-mb-1">
                    <i class="iconfont icon-file"></i>
                    {{$value}}
                </a>
            @else
                <span class="ub-text-muted">-</span>
            @endif
        </div>
        <input type="hidden" name="{{$name}}" value="{{$value}}"/>
    </div>
</div>
