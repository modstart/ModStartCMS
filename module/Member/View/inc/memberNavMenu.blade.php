@foreach($items as $i)
    <a class="sub-nav-item"
       @if($i['dialog'])
       data-dialog-request="{{$i['url']}}" href="javascript:;"
       @else
       href="{{$i['url']}}"
       @endif
    >
        {{$i['title']}}
    </a>
@endforeach
