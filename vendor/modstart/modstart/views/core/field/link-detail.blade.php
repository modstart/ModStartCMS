<div class="line">
    <div class="label">
        {{$label}}:
    </div>
    <div class="field">
        <div class="value">
            @if(!empty($value))
                <a href="{{$value}}" target="_blank">{{$value}}</a>
            @endif
        </div>
    </div>
</div>
