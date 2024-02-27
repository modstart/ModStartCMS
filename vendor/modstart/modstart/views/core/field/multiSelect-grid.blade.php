@if(!empty($value))
    @foreach($value as $v)
        @if(isset($options[$v]))
            <span class="ub-tag">{{$options[$v]}}</span>
        @else
            <span class="ub-tag">{{$v}}</span>
        @endif
    @endforeach
@else
    <span class="ub-text-muted">-</span>
@endif
