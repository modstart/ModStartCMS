@if(is_array($value))
    @foreach($value as $v)
        <a href="{{$v}}" target="_blank">
            <i class="iconfont icon-file"></i>
            {{L('View File')}}
        </a>
    @endforeach
@else
    <span class="ub-text-muted">-</span>
@endif
