@if(!empty($value))
    <pre style="margin:0;line-height:1rem;overflow:auto;width:{{$width}};">{{json_encode($value,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)}}</pre>
@endif
