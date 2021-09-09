<div class="line">
    <div class="label">
        {{$label}}:
    </div>
    <div class="field">
        @if(!empty($value['type']))
            @if($field['type']=='Text')
                <span class="ub-tag">{{L('Text')}}</span>
                <span class="ub-tag">{{$fieldName}}</span>
            @elseif($field['type']=='Radio')
                <span class="ub-tag">{{L('Radio')}}</span>
            @endif
        @else
            <span class="ub-empty">无</span>
        @endif
    </div>
</div>


