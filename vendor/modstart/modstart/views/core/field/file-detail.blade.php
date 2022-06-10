<div class="line">
    <div class="label">
        {{$label}}:
    </div>
    <div class="field">
        <div class="value">
            @if(!empty($value))
                <a href="{{$value}}" target="_blank">{{$value}}</a>
            @else
                <span class="ub-text-muted">-</span>
            @endif
        </div>
        <input type="hidden" name="{{$name}}" value="{{$value}}"/>
    </div>
</div>
