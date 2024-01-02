<div class="line" data-field id="{{$id}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        <div style="position:relative;max-width:9.4rem;">
            <input type="text"
                   {{$readonly?'readonly':''}}
                   class="form"
                   name="{{$name}}"
                   placeholder="{{$placeholder}}"
                   style="max-width:100%;"
                   autocomplete="off"
                   value="{{null===$value?$defaultValue:$value}}" />
            <img data-captcha src="{{$url}}"
                 style="height:29px;border-radius:3px;cursor:pointer;position:absolute;right:0px;top:1px;bottom:1px;border-left:1px solid #EEE;"
                 title="{{L('Click To Refresh')}}"
                 onclick="this.src='{{$url}}?'+Math.random();"/>
        </div>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
