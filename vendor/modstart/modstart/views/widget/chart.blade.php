{!! \ModStart\ModStart::js('asset/vendor/echarts/echarts.all.js') !!}
{!! \ModStart\ModStart::script("
var ele = document.getElementById(".json_encode($id).");
var chart = echarts.init(ele);
chart.setOption(".json_encode(empty($option)?new \stdClass():$option).");
MS.ui.onResize( ele, chart.resize );
$(ele).data('chart',chart);
") !!}
<div id="{{$id}}" style="height:{{empty($height)?300:$height}}px;"></div>
