<div class="line">
    <div class="label">
        {{$label}}:
    </div>
    <div class="field">
        @if(!empty($value))
            <pre style="margin:0;line-height:1rem;overflow:auto;">{{json_encode($value,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)}}</pre>
        @else
            <span class="ub-text-muted">-</span>
        @endif

    </div>
</div>
