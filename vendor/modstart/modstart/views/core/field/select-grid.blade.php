@if(isset($options[$value]))
    {{$options[$value]}}
@else
    @if($value)
        {{$value}}
    @else
        <span class="ub-text-muted">-</span>
    @endif
@endif
