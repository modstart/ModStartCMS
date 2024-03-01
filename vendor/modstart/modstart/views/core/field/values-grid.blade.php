<div class="tw-w-auto tw-break-normal tw-whitespace-normal">
    @if(!empty($value))
        @foreach($value as $v)
            @if(isset($tags[$v]))
                <span class="ub-tag">{{$tags[$v]}}</span>
            @else
                <span class="ub-tag">{{$v}}</span>
            @endif
        @endforeach
    @endif
</div>
