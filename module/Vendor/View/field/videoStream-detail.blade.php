<div class="line">
    <div class="label">
        {{$label}}:
    </div>
    <div class="field">
        <div class="value">
            @if(!empty($value['driver']))
                <span class="ub-tag">{{$value['driver']}}</span>
            @endif
            @if(!empty($value['name']))
                <span class="ub-tag">名称：{{$value['name']}}</span>
            @endif
            @if(!empty($value['path']))
                <span class="ub-tag">Path：{{$value['path']}}</span>
            @endif
        </div>
    </div>
</div>
