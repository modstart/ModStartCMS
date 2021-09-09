@if(isset($options[$value]))
    {{$options[$value]}}
@else
    <span class="ub-text-muted">-</span>
@endif