<div class="line">
    <div class="label">
        {{$label}}:
    </div>
    <div class="field">
        @if(isset($options[$value]))
            {{$options[$value]}}
        @else
            <span class="ub-text-muted">-</span>
        @endif
    </div>
</div>