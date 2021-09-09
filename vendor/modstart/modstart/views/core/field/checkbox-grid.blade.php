@foreach($value as $v)
    @if(isset($options[$v]))
        <span class="ub-tag">{{$options[$v]}}</span>
    @else
        <span class="ub-tag">{{$v}}</span>
    @endif
@endforeach
