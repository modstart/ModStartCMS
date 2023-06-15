{{--delete at 2024-06-15--}}
@foreach($value as $v)
    <div>
        <a href="{{$v}}" target="_blank">
            <i class="iconfont icon-file"></i>
            {{$v}}
        </a>
    </div>
@endforeach
