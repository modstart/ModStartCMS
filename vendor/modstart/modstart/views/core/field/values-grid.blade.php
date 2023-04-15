<div class="tw-w-auto tw-break-normal tw-whitespace-normal">
    @foreach($value as $v)
        @if(isset($tags[$v]))
            <span class="ub-tag sm">{{$tags[$v]}}</span>
        @else
            <span class="ub-tag sm">{{$v}}</span>
        @endif
    @endforeach
</div>
