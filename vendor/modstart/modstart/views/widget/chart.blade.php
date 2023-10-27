{!! \ModStart\ModStart::js('asset/vendor/echarts/echarts.all.js') !!}
{!! \ModStart\ModStart::script("
var ele = document.getElementById(".\ModStart\Core\Util\SerializeUtil::jsonEncode($id).");
var chart = echarts.init(ele);
chart.setOption(".\ModStart\Core\Util\SerializeUtil::jsonEncode(empty($option)?new \stdClass():$option).");
MS.ui.onResize( ele, chart.resize );
$(ele).data('chart',chart);
") !!}
<div id="{{$id}}" style="height:{{empty($height)?300:$height}}px;"></div>
