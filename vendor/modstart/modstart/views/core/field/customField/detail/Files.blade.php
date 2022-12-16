@foreach($value as $v)
    <div>
        <a href="{{$v}}" target="_blank">
            <i class="iconfont icon-file"></i>
            {{$v}}
        </a>
    </div>
@endforeach
