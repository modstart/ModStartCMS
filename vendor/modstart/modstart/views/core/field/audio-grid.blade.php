@if(!empty($previewPlay))
    <audio controls preload="none" src="{{$value}}"></audio>
@else
    {{$value}}
@endif
