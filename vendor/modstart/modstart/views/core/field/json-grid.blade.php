@if(!empty($value))
    <pre style="margin:0;line-height:1rem;overflow:auto;width:{{$width}};">{{\ModStart\Core\Util\SerializeUtil::jsonEncodePretty($value)}}</pre>
@endif
