<div class="line">
    <div class="label">
        {{$label}}:
    </div>
    <div class="field">
        @if(!empty($value))
            <pre style="margin:0;overflow:auto;width:{{$width}};max-height:{{$maxHeight}}">{{$value}}</pre>
        @endif
    </div>
</div>