@foreach($css as $c)
<link rel="stylesheet" href="{!! \ModStart\Core\Assets\AssetsUtil::fix($c) !!}" />
@endforeach
