@foreach($js as $j)
<script src="{{ \ModStart\Core\Assets\AssetsUtil::fix($j) }}"></script>
@endforeach