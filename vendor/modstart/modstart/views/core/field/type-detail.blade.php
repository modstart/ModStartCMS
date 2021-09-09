<div class="line">
    <div class="label">
        {{$label}}:
    </div>
    <div class="field">
        @if(!empty($colorMap))
            <span class="ub-text-{{isset($colorMap[$value])?$colorMap[$value]:'default'}}">
                @if(isset($valueMap[$value]))
                    {{$valueMap[$value]}}
                @else
                    {{$value}}
                @endif
            </span>
        @else
            @if(isset($valueMap[$value]))
                {{$valueMap[$value]}}
            @else
                {{$value}}
            @endif
        @endif
    </div>
</div>