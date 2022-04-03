<div class="line">
    <div class="label">
        {{$label}}:
    </div>
    <div class="field">
        @if(isset($options[$value]))
            {{$options[$value]}}
        @else
            @if($value)
                {{$value}}
            @else
                <span class="ub-text-muted">-</span>
            @endif
        @endif
    </div>
</div>
