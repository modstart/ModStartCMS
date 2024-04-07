@if(!empty($value))
    <div class="tw-w-auto tw-break-normal tw-whitespace-normal">
        @foreach($value as $v)
            @if(isset($tags[$v]))
                <span class="ub-tag">{{$tags[$v]}}</span>
            @else
                <span class="ub-tag">{{$v}}</span>
            @endif
        @endforeach
    </div>
@endif
