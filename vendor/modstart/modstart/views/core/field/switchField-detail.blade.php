<div class="line">
    <div class="label">
        {{$label}}:
    </div>
    <div class="field">
        @if(!empty($value))
            <span class="ub-text-success">{{$options[1]}}</span>
        @else
            <span class="ub-text-muted">{{$options[0]}}</span>
        @endif
    </div>
</div>